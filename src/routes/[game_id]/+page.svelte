<script lang="ts">
	import { page } from '$app/state';
	import { RDStore } from '$lib/RDStore.svelte';
	import { onMount } from 'svelte';
	import ScreenLog from '$lib/components/ScreenLog.svelte';
	// let gameState: string;
	let link: string;
	let isPlayer1: boolean = false;
	$: player1 = RDStore.value.player1;
	let player2: string;
	let game_id: string;
	$: gameState = RDStore.value.gameState;
	onMount(() => {
		game_id = RDStore.value.game_id;

		console.log('RDStore.value', RDStore.value);
		// console.log('gameState', gameState);
		link = window.location.href;
		// check ending with #1
		if (link.endsWith('#1')) {
			link = link.slice(0, -2);
			isPlayer1 = true;
		}
		const URL = window.location.href;
		// player1 = RDStore.value.player1;
		player2 = RDStore.value.player2;
		console.log('player1', player1);
		console.log('player2', player2);
		if (game_id) {
			gameState = 'play';
		} else {
			gameState = 'invited';
		}
		if (!player1) {
			console.log('no player1', RDStore.value);
			gameState = 'invalid';
		}
	});
</script>

<footer class="fixed bottom-0 left-0 z-50 w-full bg-gray-50 px-4 py-3 lg:py-4 dark:bg-gray-800">
	<p class="text-center text-sm text-gray-500">
		RDStore.value.gameState: {RDStore.value.gameState} <br />{gameState}
	</p>
</footer>

{#if isPlayer1}
	<ScreenLog title="isPlayer1" data={isPlayer1} />
	<!-- <ScreenLog title="Player1" data={player1} /> -->
{/if}
<!-- <ScreenLog title="Player1" data={player1} /> -->
<ScreenLog title="Player2" data={player2} />

{#if gameState === 'play'}
	<div class="mb-8 bg-gray-50 px-4 py-3 leading-relaxed lg:py-4 dark:bg-gray-800">
		<p class="mb-8 leading-relaxed">
			Share this link with your friend. <br /> Your friend will enter the link and the game will start.
		</p>
		<input
			type="text"
			id="hero-field"
			name="hero-field"
			bind:value={link}
			disabled
			class="w-full rounded border border-gray-300 bg-gray-100 bg-opacity-50 px-3 py-1 text-base leading-8 text-gray-700 outline-none transition-colors duration-200 ease-in-out focus:border-indigo-500 focus:bg-transparent focus:ring-2 focus:ring-indigo-200"
		/>
		<p class="mt-8 leading-relaxed">Then click the button below to start the game.</p>
		<button
			onclick={() => (RDStore.value.gameState = 'playing')}
			class=" m-4 inline-flex rounded border-0 bg-indigo-500 px-6 py-2 text-lg text-white hover:bg-indigo-600 focus:outline-none"
		>
			Play
		</button>
	</div>
{:else if gameState === 'invited'}
	<div class="mb-8 leading-relaxed">
		<p>To play, you need to enter your name and submit it.</p>
	</div>
{:else if gameState === 'playing'}
	<button
		class="inline-flex rounded border-0 bg-indigo-500 px-6 py-2 text-lg text-white hover:bg-indigo-600 focus:outline-none"
	>
		Play
	</button>
{/if}
