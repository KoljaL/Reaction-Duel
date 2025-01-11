<script lang="ts">
	import { page } from '$app/state';
	import { RDStore } from '$lib/RDStore.svelte';
	import { onMount } from 'svelte';
	import { on } from 'svelte/events';

	onMount(() => {
		console.log(RDStore.value);
	});

	const game_id = page.params.game_id;
</script>

<h1>Game {game_id}</h1>
