import { untrack } from 'svelte';

export class ReaperStore<T> {
	#key: string;
	#state: T;
	#listeners: number = 0; // Tracks active listeners
	#version = $state(0); // Automatically reactive version counter
	#proxies = new WeakMap<object, object>(); // To cache proxies for deep reactivity
	// #value: T | undefined;

	// Custom serialization/deserialization methods
	#serialize: (val: T) => string = JSON.stringify;
	#deserialize: (val: string) => T = JSON.parse;

	/**
	 * Constructor for initializing the store.
	 * @param key - A unique key for localStorage persistence.
	 * @param initialValue - The default state value.
	 * @param options - Optional custom serialization and deserialization methods.
	 */
	constructor(
		key: string,
		initialValue: T,
		options?: { serialize?: (val: T) => string; deserialize?: (val: string) => T }
	) {
		// Store key and initial value
		this.#key = key;
		// Reactive counter
		this.#version = 0;
		// Use the provided or default serialize/deserialize methods
		if (options?.serialize) this.#serialize = options.serialize;
		if (options?.deserialize) this.#deserialize = options.deserialize;

		// if (typeof localStorage !== 'undefined') {
		try {
			// Attempt to load the stored value from localStorage
			untrack(() => {
				const storedValue = localStorage.getItem(this.#key);
				if (storedValue !== null) {
					// Deserialize from localStorage if available
					this.#state = this.#deserialize(storedValue);
				} else {
					// If no stored value, set to the initial value and persist
					this.#state = initialValue;
					this.#persistState(initialValue);
				}
				window.addEventListener('storage', this.#handleStorageEvent);
			});
		} catch (error) {
			console.error(`Failed to initialize store '${this.#key}':`, error);
			this.#state = initialValue; // Use initial value if deserialization fails
		}

		// Sync changes across tabs using storage event listener
		$effect.root(() => {
			// console.info('ReaperStore constructor', this.#key, this.#handleStorageEvent);
			// Only add the event listener when there's an active effect tracking
			if (this.#listeners === 0) {
				window.addEventListener('storage', this.#handleStorageEvent);
			}
			this.#listeners++;

			return () => {
				this.#listeners--;
				if (this.#listeners === 0) {
					window.removeEventListener('storage', this.#handleStorageEvent);
				}
			};
		});
		// }
	}

	/**
	 * Reactive getter for accessing the state value.
	 * Returns the state as a reactive proxy for deep reactivity.
	 */
	get value(): T {
		this.#version;
		// `$state` makes the state deeply reactive, so no need for proxies
		return this.#createProxy(this.#state);
		// return this.#state;
	}

	/**
	 * Setter for replacing the entire state and persisting to localStorage.
	 * @param newValue - The new state value to set.
	 */
	set value(newValue: T) {
		if (typeof localStorage !== 'undefined') {
			this.#state = newValue; // Automatically triggers reactivity with `$state`
			this.#persistState(newValue); // Persist the updated state to localStorage
		}
		this.#version += 1; // Reactively update version to trigger any dependent effects
	}

	// set current(value) {
	// 	if (typeof localStorage !== 'undefined') {
	// 		localStorage.setItem(this.#key, JSON.stringify(value));
	// 	}
	// 	this.#version += 1;
	// }

	/**
	 * Creates a proxy for deeply reactive state, minimizing overhead.
	 * @param value - The object or array to proxy.
	 */
	#createProxy<V extends object | null | undefined>(value: V): V {
		// console.info('Creating proxy for', this.#key, value);
		if (typeof value !== 'object' || value === null) return value;

		if (!this.#proxies.has(value)) {
			const proxy = new Proxy(value, {
				get: (target, prop) => {
					this.#version;
					// this.#trackListeners();
					return this.#createProxy(Reflect.get(target, prop));
				},
				set: (target, prop, newValue) => {
					this.#version += 1; // Increment a reactive counter
					Reflect.set(target, prop, newValue);
					this.#persistState(this.#state);
					return true;
				}
			});
			this.#proxies.set(value, proxy);
		}

		return this.#proxies.get(value);
	}

	/**
	 * Persists the state to localStorage with error handling.
	 * @param value - The value to persist.
	 */
	#persistState(value: T) {
		try {
			// Serialize and save the state to localStorage
			localStorage.setItem(this.#key, this.#serialize(value));
		} catch (error) {
			console.error(`Failed to persist store '${this.#key}':`, error);
		}
	}

	/**
	 * Handles storage events for cross-tab synchronization.
	 * @param event - The storage event triggered.
	 */
	#handleStorageEvent = (event: StorageEvent) => {
		if (event.key === this.#key && event.newValue !== null) {
			// console.log('storage event', event);
			try {
				const newValue = this.#deserialize(event.newValue);
				if (JSON.stringify(this.#state) !== JSON.stringify(newValue)) {
					// Only update if the state has actually changed
					untrack(() => {
						this.#state = newValue;
						this.#version += 1; // Trigger reactivity
					});
				}
			} catch (error) {
				console.error(`Failed to handle storage event for '${this.#key}':`, error);
			}
		}
	};

	/**
	 * Cleans up event listeners when the store is no longer used.
	 */
	destroy() {
		window.removeEventListener('storage', this.#handleStorageEvent);
	}

	/**
	 * Static method to create a `ReaperStore` from `localStorage`.
	 * @param key - The key in `localStorage`.
	 * @param options - Optional custom serialization and deserialization methods.
	 * @returns A new `ReaperStore` initialized with the value from `localStorage`, or `null` if the key does not exist.
	 */
	static fromLocalStorage<T>(
		key: string,
		options?: { serialize?: (val: T) => string; deserialize?: (val: string) => T }
	): ReaperStore<T> | null {
		try {
			const storedValue = localStorage.getItem(key);
			if (storedValue !== null) {
				const initialValue = options?.deserialize
					? options.deserialize(storedValue)
					: JSON.parse(storedValue);
				return new ReaperStore(key, initialValue, options);
			} else {
				console.warn(`Key '${key}' not found in localStorage.`);
				return null;
			}
		} catch (error) {
			console.error(`Failed to create store from localStorage for '${key}':`, error);
			return null;
		}
	}

	/**
	 * Static method to retrieve all `ReaperStore` instances from `localStorage` with a specific prefix.
	 * @param prefix - The prefix for store keys in `localStorage`.
	 * @param options - Optional custom serialization and deserialization methods.
	 * @returns A record of `ReaperStore` instances keyed by their `localStorage` keys.
	 */
	static getAll<T>(
		prefix: string,
		options?: { serialize?: (val: T) => string; deserialize?: (val: string) => T }
	): Record<string, ReaperStore<T>> {
		const allStores: Record<string, ReaperStore<T>> = {};

		for (let i = 0; i < localStorage.length; i++) {
			const key = localStorage.key(i);
			if (key && key.startsWith(prefix)) {
				try {
					const storedValue = localStorage.getItem(key);
					const initialValue = options?.deserialize
						? options.deserialize(storedValue || '{}')
						: JSON.parse(storedValue || '{}');
					allStores[key] = new ReaperStore(key, initialValue, options);
				} catch (error) {
					console.error(`Failed to parse store from localStorage for '${key}':`, error);
				}
			}
		}

		return allStores;
	}

	/**
	 * Static method to create a prefixed `ReaperStore`.
	 * @param prefix - The prefix to prepend to the key.
	 * @param key - The key for `localStorage`.
	 * @param initialValue - The initial value for the store state.
	 * @param options - Optional custom serialization and deserialization methods.
	 * @returns A new `ReaperStore` instance.
	 */
	static createWithPrefix<T>(
		prefix: string,
		key: string,
		initialValue: T,
		options?: { serialize?: (val: T) => string; deserialize?: (val: T) => T }
	): ReaperStore<T> {
		const prefixedKey = `${prefix}:${key}`;
		return new ReaperStore(prefixedKey, initialValue, options);
	}
}
