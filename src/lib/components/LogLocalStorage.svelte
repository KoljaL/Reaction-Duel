<script lang="ts">
	// Function to log messages with a specific style
	function logWrapper(key: string, value: any, type: 'log' | 'warn' | 'error' = 'log') {
		const styles = {
			log: 'color: dodgerblue',
			warn: 'color: orange',
			error: 'color: red'
		};
		console.info(`%c${key}`, styles[type], value);
	}

	function showAllLocalStorage() {
		const keys = Object.keys(localStorage);
		if (!keys.length) {
			logWrapper('No items in localStorage', 'really nothing', 'warn');
			return;
		}
		logWrapper('%cItems in localStorage:', 'color:dodgerblue;font-weight:bold;');
		keys.forEach((key) => {
			const item = localStorage.getItem(key);
			if (item) {
				logItem(key, item);
			}
		});
		// console.log('\n');
	}

	// localStorage.setItem('test', 'test');

	function logItem(key: string, item: string) {
		if (
			item.startsWith('{') ||
			item.startsWith('[') ||
			item === 'true' ||
			item === 'false' ||
			!isNaN(Number(item)) ||
			item === 'null' ||
			item === 'undefined'
		) {
			logWrapper(key, JSON.parse(item));
		} else {
			logWrapper(key, item);
		}
	}

	function clearAllLocalStorage() {
		localStorage.clear();
		showAllLocalStorage();
	}
</script>

<div class="fixedtopright">
	<button onclick={showAllLocalStorage}>Show</button>
	<button onclick={clearAllLocalStorage}>Clear</button>
</div>

<style>
	.fixedtopright {
		grid-area: loglocal;
		justify-self: right;
		/* position: fixed;
        top: 20px;
        right: 20px; */
		display: flex;
	}
	button {
		appearance: none;
		background-color: #fafbfc;
		border: 1px solid rgba(27, 31, 35, 0.15);
		border-radius: 6px;
		box-shadow:
			rgba(27, 31, 35, 0.04) 0 1px 0,
			rgba(255, 255, 255, 0.25) 0 1px 0 inset;
		box-sizing: border-box;
		color: #24292e;
		cursor: pointer;
		display: inline-block;
		font-family: 'JetBrains Mono', monospace;
		font-size: 12px;
		font-weight: 500;
		line-height: 10px;
		padding: 4px 8px;
		transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
		user-select: none;
		-webkit-user-select: none;
		touch-action: manipulation;
		vertical-align: middle;
		white-space: nowrap;
		word-wrap: break-word;
	}

	button:hover {
		background-color: #f3f4f6;
		border-color: rgba(27, 31, 35, 0.15);
	}
	button:active {
		background-color: #edeff2;
		border-color: rgba(27, 31, 35, 0.15);
	}
</style>
