!function (t, o) {
    "function" == typeof define && define.amd ? define(o) : "object" == typeof exports ? module.exports = o() : t.tingle = o()
}(this, function () {
    function t(t) {
        var o = {
            onClose: null,
            onOpen: null,
            beforeClose: null,
            stickyFooter: !1,
            footer: !1,
            cssClass: [],
            closeLabel: "Close",
            closeMethods: ["overlay", "button", "escape"]
        };
        this.opts = h({}, o, t), this.init()
    }

    function o() {
        this.modal.classList.contains("tingle-modal--visible") && (this.isOverflow() ? this.modal.classList.add("tingle-modal--overflow") : this.modal.classList.remove("tingle-modal--overflow"), !this.isOverflow() && this.opts.stickyFooter ? this.setStickyFooter(!1) : this.isOverflow() && this.opts.stickyFooter && (e.call(this), this.setStickyFooter(!0)))
    }

    function e() {
        this.modalBoxFooter && (this.modalBoxFooter.style.width = this.modalBox.clientWidth + "px", this.modalBoxFooter.style.left = this.modalBox.offsetLeft + "px")
    }

    function s() {
        this.modal = document.createElement("div"), this.modal.classList.add("tingle-modal"), 0 !== this.opts.closeMethods.length && this.opts.closeMethods.indexOf("overlay") !== -1 || this.modal.classList.add("tingle-modal--noOverlayClose"), this.modal.style.display = "none", this.opts.cssClass.forEach(function (t) {
            "string" == typeof t && this.modal.classList.add(t)
        }, this), this.opts.closeMethods.indexOf("button") !== -1 && (this.modalCloseBtn = document.createElement("button"), this.modalCloseBtn.classList.add("tingle-modal__close"), this.modalCloseBtnIcon = document.createElement("span"), this.modalCloseBtnIcon.classList.add("tingle-modal__closeIcon"), this.modalCloseBtnIcon.innerHTML = "×", this.modalCloseBtnLabel = document.createElement("span"), this.modalCloseBtnLabel.classList.add("tingle-modal__closeLabel"), this.modalCloseBtnLabel.innerHTML = this.opts.closeLabel, this.modalCloseBtn.appendChild(this.modalCloseBtnIcon), this.modalCloseBtn.appendChild(this.modalCloseBtnLabel)), this.modalBox = document.createElement("div"), this.modalBox.classList.add("tingle-modal-box"), this.modalBoxContent = document.createElement("div"), this.modalBoxContent.classList.add("tingle-modal-box__content"), this.modalBox.appendChild(this.modalBoxContent), this.opts.closeMethods.indexOf("button") !== -1 && this.modal.appendChild(this.modalCloseBtn), this.modal.appendChild(this.modalBox)
    }

    function i() {
        this.modalBoxFooter = document.createElement("div"), this.modalBoxFooter.classList.add("tingle-modal-box__footer"), this.modalBox.appendChild(this.modalBoxFooter)
    }

    function n() {
        this._events = {
            clickCloseBtn: this.close.bind(this),
            clickOverlay: d.bind(this),
            resize: o.bind(this),
            keyboardNav: l.bind(this)
        }, this.opts.closeMethods.indexOf("button") !== -1 && this.modalCloseBtn.addEventListener("click", this._events.clickCloseBtn), this.modal.addEventListener("mousedown", this._events.clickOverlay), window.addEventListener("resize", this._events.resize), document.addEventListener("keydown", this._events.keyboardNav)
    }

    function l(t) {
        this.opts.closeMethods.indexOf("escape") !== -1 && 27 === t.which && this.isOpen() && this.close()
    }

    function d(t) {
        this.opts.closeMethods.indexOf("overlay") !== -1 && !a(t.target, "tingle-modal") && t.clientX < this.modal.clientWidth && this.close()
    }

    function a(t, o) {
        for (; (t = t.parentElement) && !t.classList.contains(o);) ;
        return t
    }

    function r() {
        this.opts.closeMethods.indexOf("button") !== -1 && this.modalCloseBtn.removeEventListener("click", this._events.clickCloseBtn), this.modal.removeEventListener("mousedown", this._events.clickOverlay), window.removeEventListener("resize", this._events.resize), document.removeEventListener("keydown", this._events.keyboardNav)
    }

    function h() {
        for (var t = 1; t < arguments.length; t++) for (var o in arguments[t]) arguments[t].hasOwnProperty(o) && (arguments[0][o] = arguments[t][o]);
        return arguments[0]
    }

    function c() {
        var t, o = document.createElement("tingle-test-transition"), e = {
            transition: "transitionend",
            OTransition: "oTransitionEnd",
            MozTransition: "transitionend",
            WebkitTransition: "webkitTransitionEnd"
        };
        for (t in e) if (void 0 !== o.style[t]) return e[t]
    }

    var m = c();
    return t.prototype.init = function () {
        this.modal || (s.call(this), n.call(this), document.body.insertBefore(this.modal, document.body.firstChild), this.opts.footer && this.addFooter())
    }, t.prototype.destroy = function () {
        null !== this.modal && (r.call(this), this.modal.parentNode.removeChild(this.modal), this.modal = null)
    }, t.prototype.open = function () {
        this.modal.style.removeProperty ? this.modal.style.removeProperty("display") : this.modal.style.removeAttribute("display"), document.body.classList.add("tingle-enabled"), this.setStickyFooter(this.opts.stickyFooter), this.modal.classList.add("tingle-modal--visible");
        var t = this;
        m ? this.modal.addEventListener(m, function o() {
            "function" == typeof t.opts.onOpen && t.opts.onOpen.call(t), t.modal.removeEventListener(m, o, !1)
        }, !1) : "function" == typeof t.opts.onOpen && t.opts.onOpen.call(t), o.call(this)
    }, t.prototype.isOpen = function () {
        return !!this.modal.classList.contains("tingle-modal--visible")
    }, t.prototype.close = function () {
        if ("function" == typeof this.opts.beforeClose) {
            var t = this.opts.beforeClose.call(this);
            if (!t) return
        }
        document.body.classList.remove("tingle-enabled"), this.modal.classList.remove("tingle-modal--visible");
        var o = this;
        m ? this.modal.addEventListener(m, function t() {
            o.modal.removeEventListener(m, t, !1), o.modal.style.display = "none", "function" == typeof o.opts.onClose && o.opts.onClose.call(this)
        }, !1) : (o.modal.style.display = "none", "function" == typeof o.opts.onClose && o.opts.onClose.call(this))
    }, t.prototype.setContent = function (t) {
        "string" == typeof t ? this.modalBoxContent.innerHTML = t : (this.modalBoxContent.innerHTML = "", this.modalBoxContent.appendChild(t))
    }, t.prototype.getContent = function () {
        return this.modalBoxContent
    }, t.prototype.addFooter = function () {
        i.call(this)
    }, t.prototype.setFooterContent = function (t) {
        this.modalBoxFooter.innerHTML = t
    }, t.prototype.getFooterContent = function () {
        return this.modalBoxFooter
    }, t.prototype.setStickyFooter = function (t) {
        this.isOverflow() || (t = !1), t ? this.modalBox.contains(this.modalBoxFooter) && (this.modalBox.removeChild(this.modalBoxFooter), this.modal.appendChild(this.modalBoxFooter), this.modalBoxFooter.classList.add("tingle-modal-box__footer--sticky"), e.call(this), this.modalBoxContent.style["padding-bottom"] = this.modalBoxFooter.clientHeight + 20 + "px") : this.modalBoxFooter && (this.modalBox.contains(this.modalBoxFooter) || (this.modal.removeChild(this.modalBoxFooter), this.modalBox.appendChild(this.modalBoxFooter), this.modalBoxFooter.style.width = "auto", this.modalBoxFooter.style.left = "", this.modalBoxContent.style["padding-bottom"] = "", this.modalBoxFooter.classList.remove("tingle-modal-box__footer--sticky")))
    }, t.prototype.addFooterBtn = function (t, o, e) {
        var s = document.createElement("button");
        return s.innerHTML = t, s.addEventListener("click", e), "string" == typeof o && o.length && o.split(" ").forEach(function (t) {
            s.classList.add(t)
        }), this.modalBoxFooter.appendChild(s), s
    }, t.prototype.resize = function () {
        console.warn("Resize is deprecated and will be removed in version 1.0")
    }, t.prototype.isOverflow = function () {
        var t = window.innerHeight, o = this.modalBox.clientHeight;
        return o >= t
    }, {modal: t}
});