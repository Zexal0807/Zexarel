export default class ZexalRouter extends HTMLElement {
	_base = '';

	constructor() {
		super();

		if (this.hasAttribute('base')) {
			this._base = this.getAttribute('base');
			this.removeAttribute('base');
		}

		if (this.getContent() == null) {
			this.innerHTML = this.innerHTML + '<zexal-content></zexal-content>';
		}
	}

	getContent() {
		return this.querySelector('zexal-content');
	}

	emptyContent() {
		this.querySelector('zexal-content').innerHTML = '';
	}

	getRoutes() {
		var routes = this.querySelectorAll('zexal-route');
		routes = Array.from(routes);
		return routes.map((route) => ({
			path: this._base + route.getAttribute('path'),
			page: route.getAttribute('page'),
			title: route.getAttribute('title')
		}));
	}

	connectedCallback() {
		this.navigate(window.location.pathname);

		window.addEventListener('popstate', this._handlePopState);
	}

	disconnectedCallback() {
		window.removeEventListener('popstate', this._handlePopState);
	}

	_handlePopState = () => {
		this.navigate(window.location.pathname);
	};

	_segmentize(uri) {
		return uri.replace(/(^\/+|\/+$)/g, '').split('/');
	}

	_match(routes, uri) {
		let match;
		const [uriPathname] = uri.split('?');
		const uriSegments = this._segmentize(uriPathname);
		const isRootUri = uriSegments[0] === '/';
		for (let i = 0; i < routes.length; i++) {
			const route = routes[i];
			const routeSegments = this._segmentize(route.path);
			const max = Math.max(uriSegments.length, routeSegments.length);
			let index = 0;
			let missed = false;
			let params = {};
			for (; index < max; index++) {
				const uriSegment = uriSegments[index];
				const routeSegment = routeSegments[index];
				const fallback = routeSegment === '*';

				if (fallback) {
					params['*'] = uriSegments
						.slice(index)
						.map(decodeURIComponent)
						.join('/');
					break;
				}

				if (uriSegment === undefined) {
					missed = true;
					break;
				}

				let dynamicMatch = /^:(.+)/.exec(routeSegment);

				if (dynamicMatch && !isRootUri) {
					let value = decodeURIComponent(uriSegment);
					params[dynamicMatch[1]] = value;
				} else if (routeSegment !== uriSegment) {
					missed = true;
					break;
				}
			}

			if (!missed) {
				match = {
					params,
					...route
				};
				break;
			}
		}

		return match || null;
	}

	navigate(url) {
		const matchedRoute = this._match(this.getRoutes(), url);
		if (matchedRoute !== null) {
			this.activeRoute = matchedRoute;
			window.history.pushState(null, null, url);
			this.update();
		}
	}

	update() {
		const { page, title, params = {} } = this.activeRoute;

		if (page) {
			this.emptyContent();

			const view = document.createElement(page);
			document.title = title || document.title;
			for (let key in params) {
				view.setAttribute(key, params[key]);
			}
			this.getContent().appendChild(view);
		}
	}

	goBack() {
		window.history.go(-1);
	}
}

customElements.define('zexal-router', ZexalRouter);

class ZexalRoute extends HTMLElement {}

customElements.define('zexal-route', ZexalRoute);