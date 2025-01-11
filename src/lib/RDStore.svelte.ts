import { ReaperStore } from '$lib/reaperStore.svelte';

type RDStoreT = ReaperStore<{
	game_id: string;
	player1: string;
	player2: string;
	gameState: 'start' | 'created' | 'invited' | 'playing' | 'finished';
	gameTimes: { [key: number]: [string, string] };
}>;

const prefix = 'ReactionDuel';
const key = 'ID';
const value = {
	game_id: '',
	player1: '',
	player2: '',
	gameState: 'start',
	gameTimes: {
		0: ['', ''],
		1: ['', ''],
		2: ['', ''],
		3: ['', ''],
		4: ['', ''],
		5: ['', ''],
		6: ['', ''],
		7: ['', ''],
		8: ['', ''],
		9: ['', '']
	}
};

export const RDStore: RDStoreT = ReaperStore.createWithPrefix(prefix, key, value);
