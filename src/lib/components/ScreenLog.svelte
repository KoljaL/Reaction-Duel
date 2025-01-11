<script lang="ts">
	export let data: unknown;
	export let title: string;
	let isVisible = false;
	let message: string | Record<string, unknown> = '';
	console.log(data);
	console.log(typeof data);
	console.log(title);

	message = logItem(data);

	function logItem(data: unknown): string | Record<string, unknown> {
		if (!data) return '';
		console.log(data);
		const result = typeof data === 'object' ? JSON.stringify(data) : String(data);
		console.log(typeof data);
		isVisible = true;
		return result;
	}

	const closeConsole = () => {
		isVisible = false;
	};
</script>

{#if isVisible}
	<div
		class="fixed bottom-0 left-0 z-50 max-h-60 w-80 overflow-y-auto rounded-t-lg bg-gray-800 p-4 text-white shadow-lg"
	>
		<div class="mb-2 flex items-center justify-between">
			<span class="font-bold">{title}</span>
			<button
				on:click={closeConsole}
				class="rounded bg-red-500 px-2 py-1 text-sm text-white hover:bg-red-600"
			>
				Close
			</button>
		</div>
		<p>{message}</p>
		<!-- {#if data.length > 0}
			<ul>
				{#each data as message, index}
					<li class="mb-1">{index + 1}: {message}</li>
				{/each}
			</ul>
		{/if} -->
	</div>
{/if}
