import adapter from '@sveltejs/adapter-auto';

const ignoreWarnings = new Set([
	'a11y-no-onchange',
	'a11y-label-has-associated-control'
])

/** @type {import('@sveltejs/kit').Config} */
const config = {
	compilerOptions: {
		warningFilter(warning) {
			return !ignoreWarnings.has(warning.code)
		}
	},
	kit: {
		adapter: adapter()
	}
};

export default config;
