<script lang="ts">
	import { ReaperStore } from '$lib/reaperStore.svelte';
	import { onMount } from 'svelte';
	// Create a ReaperStore instance for user preferences
	const userPreferences = new ReaperStore('user:preferences', {
		theme: 'light',
		fontSize: 16,
		notifications: true
	});

	onMount(() => {
		// Set the theme from the user preferences
		document.documentElement.dataset.theme = userPreferences.value.theme;
	});

	// Synchronize changes for real-time interaction
	function toggleTheme() {
		userPreferences.value = {
			...userPreferences.value,
			theme: userPreferences.value.theme === 'light' ? 'dark' : 'light'
		};
		// toggle data-theme in the document
		document.documentElement.dataset.theme = userPreferences.value.theme;
	}
	$inspect(userPreferences.value);
</script>

<button onclick={toggleTheme}>
	{userPreferences.value.theme === 'light' ? 'Switch to Dark' : 'Switch to Light'}
</button>

<style>
	button {
		grid-area: toggle;
		align-self: center;
		justify-self: right;
		cursor: pointer;
	}
</style>
