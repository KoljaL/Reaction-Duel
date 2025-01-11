<script lang="ts">
	import '../app.css';
	// import { ReaperStore } from '$lib/reaperStore.svelte';

	// // type RCStoreT = {
	// // 	game_id: string;
	// // 	player1: string;
	// // 	player2: string;
	// // 	gameState: string;
	// // 	gameTimes: { [key: number]: [string, string] };
	// // };

	// let RCStore: ReaperStore<{
	// 	game_id: string;
	// 	player1: string;
	// 	player2: string;
	// 	gameState: 'start' | 'created' | 'invited';
	// 	gameTimes: { [key: number]: [string, string] };
	// }>;

	// const prefix = 'ReactionDuel';
	// const key = 'ID';
	// const value = {
	// 	game_id: '',
	// 	player1: '',
	// 	player2: '',
	// 	gameState: 'start',
	// 	gameTimes: {
	// 		0: ['', ''],
	// 		1: ['', ''],
	// 		2: ['', ''],
	// 		3: ['', ''],
	// 		4: ['', ''],
	// 		5: ['', ''],
	// 		6: ['', ''],
	// 		7: ['', ''],
	// 		8: ['', ''],
	// 		9: ['', '']
	// 	}
	// };

	// RCStore = ReaperStore.createWithPrefix(prefix, key, value);

	let { children } = $props();
</script>

{@render children()}
