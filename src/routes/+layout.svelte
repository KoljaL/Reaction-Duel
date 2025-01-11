<script lang="ts">
	// import '$lib/style/classless.css';
	import LogLocalStorage from '$lib/components/LogLocalStorage.svelte';
	import '$lib/style/app.css';

	let { children } = $props();
</script>

<LogLocalStorage />
<header
	class="sticky top-0 z-50 flex w-full justify-center border-b bg-gray-50 px-4 py-3 lg:py-4 dark:bg-gray-800"
>
	<h1 class="title-font mb-4 text-3xl font-medium text-blue-700 sm:text-4xl">
		<a href="/" class="hover:text-blue-500">ReactionDuel</a>
	</h1>
</header>

<main class="body-font text-gray-300">
	<div class="container mx-auto flex flex-col items-center justify-center px-5 py-24">
		<div class="mb-16 flex w-full flex-col items-center text-center md:w-2/3">
			{@render children()}
		</div>
	</div>
</main>
