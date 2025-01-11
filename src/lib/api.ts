const URL = 'https://dev.rasal.de/reaction/api/index.php';

export type Options = {
	method: string;
	body: string | null;
	params?: { [key: string]: string } | null;
};

export const api = async (options: Options) => {
	options.headers = { 'Content-Type': 'application/json' };
	try {
		const url = URL + '?' + new URLSearchParams(options.params).toString();
		const response = await fetch(url, options);
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		const resp = await response.json();
		console.log('ACT', resp.request.action || 'NO ACTION');
		console.log('REQ', resp.request);
		delete resp.request;
		console.log('RES', resp);

		return resp;
	} catch (error) {
		console.error('There was a problem with your fetch operation:', error);
	}
};
