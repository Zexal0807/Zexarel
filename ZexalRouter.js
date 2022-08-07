export default class ZexalRouter extends HTMLElement {
	constructor() {
		super();
		if (this.content() == null) {
			this.innerHTML = this.innerHTML + '<zexal-content></zexal-content>';
		}
	}

	content() {
		return this.querySelector('zexal-content');
	}

	routes() {
		var routes = this.querySelectorAll('zexal-route');
		return Array.from(routes).map((route) => ({
			path: route.getAttribute('path'),
			page: route.getAttribute('page'), // Page
			title: route.getAttribute('title') // Optional: Document title
		}));
	}

	connectedCallback() {
		this.updateLinks();
		this.navigate(window.location.pathname);

		window.addEventListener('popstate', this._handlePopstate);
	}

	disconnectedCallback() {
		window.removeEventListener('popstate', this._handlePopstate);
	}

	_handlePopstate = () => {
		this.navigate(window.location.pathname);
	};

	updateLinks() {
		this.querySelectorAll('a[route]').forEach((link) => {
			const target = link.getAttribute('route');
			link.setAttribute('href', target);
			link.onclick = (e) => {
				e.preventDefault();
				this.navigate(target);
			};
		});
	}

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
		const matchedRoute = this._match(this.routes(), url);
		if (matchedRoute !== null) {
			this.activeRoute = matchedRoute;
			window.history.pushState(null, null, url);
			this.update();
		}
	}

	update() {
		const { page, title, params = {} } = this.activeRoute;

		if (page) {
			// Remove all child nodes in contetn element
			while (this.content().firstChild) {
				this.content().removeChild(this.content().firstChild);
			}

			const view = document.createElement(page);
			document.title = title || document.title;
			for (let key in params) {
				if (key !== '*') view.setAttribute(key, params[key]);
			}
			this.content().appendChild(view);

			// Update the route links once the DOM is updated
			this.updateLinks();
		}
	}

	go(url) {
		this.navigate(url);
	}

	back() {
		window.history.go(-1);
	}
}

customElements.define('zexal-router', ZexalRouter);

class ZexalRoute extends HTMLElement {}

customElements.define('zexal-route', ZexalRoute);