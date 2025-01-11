<script lang="ts">
	import { api } from '$lib/api';
	import { onMount } from 'svelte';

	let user1 = '';

	const createGame = async () => {
		const response = await api({
			method: 'POST',
			body: JSON.stringify({ action: 'getGames' }),
			params: { user: 'test' }
		});
		console.log(response);
	};
</script>

<h1>Page</h1>

<label for="user1">
	User 1:
	<input type="text" id="user1" bind:value={user1} />
	<button onclick={createGame}>Send</button>
</label>
