var isMobile = false;
if (screen.width <= 480) {
	isMobile = true;
	console.log("Mobile");
}

class ZexalComponent extends HTMLElement {
	attributeChangedCallback(name, oldValue, newValue) {
		this.render();
	}

	_style = null;

	constructor() {
		super();
	}

	connectedCallback() {
		this.render();
	}

	_render() {
		return "";
	}

	_addEvent() {}

	render() {
		this.innerHTML = "";

		var content = this._render();
		if (content instanceof HTMLElement) {
			this.append(content);
		} else if (
			Array.isArray(content) &&
			content.filter((element) => element instanceof HTMLElement).length ==
			content.length
		) {
			content.forEach((element) => {
				this.append(element);
			});
		} else {
			this.innerHTML = content;
		}

		this._addEvent();
		if (this._style != null) {
			if (
				document.querySelectorAll("link[href='" + this._style + "']").length ==
				0
			) {
				var style = document.createElement("link");
				style.setAttribute("rel", "stylesheet");
				style.setAttribute("href", this._style);
				document.head.append(style);
			}
		}
	}
}