<script lang="ts">
	import { api } from '$lib/api';
	import { onMount } from 'svelte';
	import { RDStore } from '$lib/RDStore.svelte';
	import { goto } from '$app/navigation';

	let user1 = 'Thomas';

	const createGame = async () => {
		const response = await api({
			method: 'POST',
			body: JSON.stringify({ action: 'create_game', player1: user1 }),
			params: { user: 'test' }
		});
		console.log(response);
		if (response.game_id) {
			console.log(response.game_id);
			RDStore.value.gameState = 'created';
			RDStore.value.game_id = response.game_id;
			RDStore.value.player1 = user1;
			// console.log(RDStore.value);
			goto(`/${response.game_id}#1`);
		}
	};
</script>

<div class="flex w-full items-end">
	<p class="mb-8 leading-relaxed">
		To play, you need to invite a friend. You will be given a code to share with your friend. Your
		friend will enter the code and the game will start.
	</p>
</div>
<div class="flex w-full items-end">
	<div class="relative mr-4 w-2/4 text-left md:w-full lg:w-full xl:w-1/2">
		<input
			type="text"
			id="user1"
			bind:value={user1}
			class="w-full rounded border border-gray-300 bg-gray-100 bg-opacity-50 px-3 py-1 text-base leading-8 text-gray-700 outline-none transition-colors duration-200 ease-in-out focus:border-indigo-500 focus:bg-transparent focus:ring-2 focus:ring-indigo-200"
		/>
	</div>
	<button
		onclick={createGame}
		class="inline-flex rounded border-0 bg-indigo-500 px-6 py-2 text-lg text-white hover:bg-indigo-600 focus:outline-none"
	>
		Submit
	</button>
</div>
<p class="mb-8 mt-2 w-full text-sm text-gray-500">funny stuff about the game</p>
