export default class ZexalRouter extends HTMLElement {
	_base = "";
	
	_historyStack;

	constructor() {
		super();

		this._historyStack = [];

		if (this.hasAttribute("base")) {
			this._base = this.getAttribute("base");
			this.removeAttribute("base");
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

	getRealUrl() {
		let url = window.location.pathname;
		url = url.substring(this._base.length);
		return url;
	}

	connectedCallback() {
		this.navigate(window.location.pathname);
		window.addEventListener("popstate", this._handlePopState);
	}

	disconnectedCallback() {
		window.removeEventListener("popstate", this._handlePopState);
	}

	_handlePopState = () => {
		let targetUrl = this._historyStack.pop()
		targetUrl = this._historyStack.pop()

		if(targetUrl == undefined)
			this.navigate(this._base + "/");

		this.navigate(targetUrl);
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
			window.history.replaceState(null, null, url);
			this._historyStack.push(url);
			this.update();
		}
	}

	_normalizeUrl(url) {
		// Se l'URL è assoluto, lo lasciamo com'è
		if (url.startsWith('/')) 
			return this._base + url.replace(/^\//, '');

		// Se è relativo, lo combiniamo
		const current = window.location.pathname.replace(this._base, '');
		const newPath = new URL(url, window.location.origin + this._base + current).pathname;

		return newPath;
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
		this._handlePopState();
	}
}

customElements.define('zexal-router', ZexalRouter);

class ZexalRoute extends HTMLElement {}

customElements.define('zexal-route', ZexalRoute);

class ZexalLink extends HTMLElement {

	connectedCallback() {
		this.style.cursor = 'pointer';
		const to = this.getAttribute('href') || '/';

		const openInNewTab = (e) => {
			e.preventDefault();

			if(e.button == 1){
				let a = document.createElement("a");
				a.href = to;
				a.target = "_blank";
				a.click();
			}
		}

		const openHere = (e) => {
			e.preventDefault();
			
			const router = document.querySelector('zexal-router');
			if (router && typeof router.navigate === 'function') {
				const base = router._base || '';
				const fullUrl = new URL(to, window.location.origin + base + "/").pathname;
				router.navigate(fullUrl);
			} else {
				// Se non esiste il router uso un classico history.pushState
				history.pushState({}, '', to);
			}
		}

		this.addEventListener('auxclick', openInNewTab);

		this.addEventListener('click', (e) => {
			if(e.ctrlKey){
				openInNewTab(e);
			}else{
				openHere(e);
			}
		});

		this.addEventListener('keydown', (e) => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				this.click();
			}
		});
	}
}

customElements.define('zexal-link', ZexalLink);