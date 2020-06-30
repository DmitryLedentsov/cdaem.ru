function initPickers(a) {
    a = 1 == a && "function" == typeof moment ? moment() : !1;
    var b = $(".datepicker"), c = $(".timepicker");
    b.length && b.datetimepicker({
        format: "DD.MM.YYYY",
        locale: "ru",
        minDate: a,
        showTodayButton: !0,
        showClear: !0,
        ignoreReadonly: !0,
        icons: {
            time: "icon-time",
            date: "icon-calendar",
            up: "icon-chevron-up",
            down: "icon-chevron-down",
            previous: "icon-chevron-left",
            next: "icon-chevron-right",
            today: "icon-screenshot",
            clear: "icon-trash"
        }
    }).on("dp.show", function (a) {
        $(a.currentTarget).parent().find("a[data-action=today]").html("Сегодня").addClass("today"), $(a.currentTarget).parent().find("a[data-action=clear]").html("Сбросить").addClass("clear")
    }), c.length && c.datetimepicker({
        format: "HH:mm",
        showClear: !0,
        ignoreReadonly: !0,
        stepping: 5,
        icons: {
            time: "icon-time",
            date: "icon-calendar",
            up: "icon-chevron-up",
            down: "icon-chevron-down",
            previous: "icon-chevron-left",
            next: "icon-chevron-right",
            today: "icon-screenshot",
            clear: "icon-trash"
        }
    }).on("dp.show", function (a) {
        $(a.currentTarget).parent().find("a[data-action=clear]").html("Сбросить").addClass("clear")
    })
}

function showStackInfo(a, b, c) {
    return "undefined" != typeof info_box ? void info_box.open() : void new PNotify({
        title: a,
        text: b,
        type: "info",
        delay: 4500,
        history: {history: !0},
        buttons: {closer: !1, sticker: !1},
        after_close: c
    })
}

function showStackError(a, b) {
    return "undefined" != typeof info_box ? void info_box.open() : void new PNotify({
        title: a,
        text: b,
        type: "error",
        delay: 4500,
        history: {history: !0},
        buttons: {closer: !1, sticker: !1}
    })
}

function fast_payment_widget(a, b) {
    $.post("/merchant/payment-widget", $.extend({service: a}, {data: b}), function (a) {
        $("#modal-payment-widget").remove(), $("body").append(a), $("#modal-payment-widget").modal("show")
    })
}

function scrollToFirstError(a, b) {
    var c = null;
    for (var d in b) {
        for (var e in a) {
            if ("*" == b[d].id.substr(-1) && -1 != e.search(b[d].id)) {
                c = e;
                break
            }
            if (b[d].id == e) {
                c = e;
                break
            }
        }
        if (c) break
    }
    if (c) {
        var f = $(".sticky-header.scroll-to-fixed-fixed").height() || 0,
            g = $("#" + c).parents(".form-group").offset().top - f - 15;
        $("html, body").stop().animate({scrollTop: g}, 600)
    }
}

function initScriptFilter() {
    var a = location.protocol + "//" + location.host, b = $("script[src]").map(function () {
        return "/" === this.src.charAt(0) ? a + this.src : this.src
    }).toArray();
    $.ajaxPrefilter("script", function (c, d, e) {
        if ("jsonp" != c.dataType) {
            var f = "/" === c.url.charAt(0) ? a + c.url : c.url;
            if (-1 === $.inArray(f, b)) b.push(f); else {
                var g = -1 !== $.inArray(f, $.map(reloadableScripts, function (b) {
                    return "/" === b.charAt(0) ? a + b : b
                }));
                g || e.abort()
            }
        }
    }), $(document).ajaxComplete(function (a, b, c) {
        var d = [];
        $("link[rel=stylesheet]").each(function () {
            -1 === $.inArray(this.href, reloadableScripts) && (-1 == $.inArray(this.href, d) ? d.push(this.href) : $(this).remove())
        })
    })
}

function initSelectPicker() {
    $(".select-orange").selectpicker({
        style: "btn-orange",
        liveSearch: !1,
        maxOptions: 1,
        showIcon: !1,
        showContent: !1,
        iconBase: "",
        tickIcon: ""
    }), $(".select-pinkround").selectpicker({
        style: "btn-pinkround",
        liveSearch: !1,
        maxOptions: 1,
        showIcon: !1,
        showContent: !1,
        iconBase: "",
        tickIcon: ""
    }), $(".select-darkgray").selectpicker({
        style: "btn-darkgray",
        liveSearch: !1,
        maxOptions: 1,
        showIcon: !1,
        showContent: !1,
        iconBase: "",
        tickIcon: ""
    }), $(".select-primary").selectpicker({
        style: "btn-primary",
        liveSearch: !1,
        maxOptions: 1,
        showIcon: !1,
        showContent: !1,
        iconBase: "",
        tickIcon: ""
    }), $(".select-white").selectpicker({
        style: "btn-white",
        liveSearch: !1,
        maxOptions: 1,
        showIcon: !1,
        showContent: !1,
        iconBase: "",
        tickIcon: ""
    })
}

function toTranslit(a) {
    return a.replace(/([а-яё])|([\s_-])|([^a-z\d])/gi, function (a, b, c, d, e) {
        if (c || d) return c ? "-" : "";
        var f = b.charCodeAt(0), g = 1025 == f || 1105 == f ? 0 : f > 1071 ? f - 1071 : f - 1039,
            h = ["yo", "a", "b", "v", "g", "d", "e", "zh", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "shch", "", "y", "", "e", "yu", "ya"];
        return h[g]
    })
}

!function (a) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], a) : a("object" == typeof exports && "function" == typeof require ? require("jquery") : jQuery)
}(function (a) {
    "use strict";

    function b(c, d) {
        var e = function () {
        }, f = this, g = {
            ajaxSettings: {},
            autoSelectFirst: !1,
            appendTo: document.body,
            serviceUrl: null,
            lookup: null,
            onSelect: null,
            width: "auto",
            minChars: 1,
            maxHeight: 300,
            deferRequestBy: 0,
            params: {},
            formatResult: b.formatResult,
            delimiter: null,
            zIndex: 9999,
            type: "GET",
            noCache: !1,
            onSearchStart: e,
            onSearchComplete: e,
            onSearchError: e,
            preserveInput: !1,
            containerClass: "autocomplete-suggestions",
            tabDisabled: !1,
            dataType: "text",
            currentRequest: null,
            triggerSelectOnValidInput: !0,
            preventBadQueries: !0,
            lookupFilter: function (a, b, c) {
                return -1 !== a.value.toLowerCase().indexOf(c)
            },
            paramName: "query",
            transformResult: function (b) {
                return "string" == typeof b ? a.parseJSON(b) : b
            },
            showNoSuggestionNotice: !1,
            noSuggestionNotice: "No results",
            orientation: "bottom",
            forceFixPosition: !1
        };
        f.element = c, f.el = a(c), f.suggestions = [], f.badQueries = [], f.selectedIndex = -1, f.currentValue = f.element.value, f.intervalId = 0, f.cachedResponse = {}, f.onChangeInterval = null, f.onChange = null, f.isLocal = !1, f.suggestionsContainer = null, f.noSuggestionsContainer = null, f.options = a.extend({}, g, d), f.classes = {
            selected: "autocomplete-selected",
            suggestion: "autocomplete-suggestion"
        }, f.hint = null, f.hintValue = "", f.selection = null, f.initialize(), f.setOptions(d)
    }

    var c = function () {
        return {
            escapeRegExChars: function (a) {
                return a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")
            }, createNode: function (a) {
                var b = document.createElement("div");
                return b.className = a, b.style.position = "absolute", b.style.display = "none", b
            }
        }
    }(), d = {ESC: 27, TAB: 9, RETURN: 13, LEFT: 37, UP: 38, RIGHT: 39, DOWN: 40};
    b.utils = c, a.Autocomplete = b, b.formatResult = function (a, b) {
        var d = a.value.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"),
            e = "(" + c.escapeRegExChars(b) + ")";
        return d.replace(new RegExp(e, "gi"), "<strong>$1</strong>")
    }, b.prototype = {
        killerFn: null, initialize: function () {
            var c, d = this, e = "." + d.classes.suggestion, f = d.classes.selected, g = d.options;
            d.element.setAttribute("autocomplete", "off"), d.killerFn = function (b) {
                0 === a(b.target).closest("." + d.options.containerClass).length && (d.killSuggestions(), d.disableKillerFn())
            }, d.noSuggestionsContainer = a('<div class="autocomplete-no-suggestion"></div>').html(this.options.noSuggestionNotice).get(0), d.suggestionsContainer = b.utils.createNode(g.containerClass), c = a(d.suggestionsContainer), c.appendTo(g.appendTo), "auto" !== g.width && c.width(g.width), c.on("mouseover.autocomplete", e, function () {
                d.activate(a(this).data("index"))
            }), c.on("mouseout.autocomplete", function () {
                d.selectedIndex = -1, c.children("." + f).removeClass(f)
            }), c.on("click.autocomplete", e, function () {
                d.select(a(this).data("index"))
            }), d.fixPositionCapture = function () {
                d.visible && d.fixPosition()
            }, a(window).on("resize.autocomplete", d.fixPositionCapture), d.el.on("keydown.autocomplete", function (a) {
                d.onKeyPress(a)
            }), d.el.on("keyup.autocomplete", function (a) {
                d.onKeyUp(a)
            }), d.el.on("blur.autocomplete", function () {
                d.onBlur()
            }), d.el.on("focus.autocomplete", function () {
                d.onFocus()
            }), d.el.on("change.autocomplete", function (a) {
                d.onKeyUp(a)
            }), d.el.on("input.autocomplete", function (a) {
                d.onKeyUp(a)
            })
        }, onFocus: function () {
            var a = this;
            a.fixPosition(), a.options.minChars <= a.el.val().length && a.onValueChange()
        }, onBlur: function () {
            this.enableKillerFn()
        }, setOptions: function (b) {
            var c = this, d = c.options;
            a.extend(d, b), c.isLocal = a.isArray(d.lookup), c.isLocal && (d.lookup = c.verifySuggestionsFormat(d.lookup)), d.orientation = c.validateOrientation(d.orientation, "bottom"), a(c.suggestionsContainer).css({
                "max-height": d.maxHeight + "px",
                width: d.width + "px",
                "z-index": d.zIndex
            })
        }, clearCache: function () {
            this.cachedResponse = {}, this.badQueries = []
        }, clear: function () {
            this.clearCache(), this.currentValue = "", this.suggestions = []
        }, disable: function () {
            var a = this;
            a.disabled = !0, clearInterval(a.onChangeInterval), a.currentRequest && a.currentRequest.abort()
        }, enable: function () {
            this.disabled = !1
        }, fixPosition: function () {
            var b = this, c = a(b.suggestionsContainer), d = c.parent().get(0);
            if (d === document.body || b.options.forceFixPosition) {
                var e = b.options.orientation, f = c.outerHeight(), g = b.el.outerHeight(), h = b.el.offset(),
                    i = {top: h.top, left: h.left};
                if ("auto" === e) {
                    var j = a(window).height(), k = a(window).scrollTop(), l = -k + h.top - f,
                        m = k + j - (h.top + g + f);
                    e = Math.max(l, m) === l ? "top" : "bottom"
                }
                if ("top" === e ? i.top += -f : i.top += g, d !== document.body) {
                    var n, o = c.css("opacity");
                    b.visible || c.css("opacity", 0).show(), n = c.offsetParent().offset(), i.top -= n.top, i.left -= n.left, b.visible || c.css("opacity", o).hide()
                }
                "auto" === b.options.width && (i.width = b.el.outerWidth() - 2 + "px"), c.css(i)
            }
        }, enableKillerFn: function () {
            var b = this;
            a(document).on("click.autocomplete", b.killerFn)
        }, disableKillerFn: function () {
            var b = this;
            a(document).off("click.autocomplete", b.killerFn)
        }, killSuggestions: function () {
            var a = this;
            a.stopKillSuggestions(), a.intervalId = window.setInterval(function () {
                a.hide(), a.stopKillSuggestions()
            }, 50)
        }, stopKillSuggestions: function () {
            window.clearInterval(this.intervalId)
        }, isCursorAtEnd: function () {
            var a, b = this, c = b.el.val().length, d = b.element.selectionStart;
            return "number" == typeof d ? d === c : document.selection ? (a = document.selection.createRange(), a.moveStart("character", -c), c === a.text.length) : !0
        }, onKeyPress: function (a) {
            var b = this;
            if (!b.disabled && !b.visible && a.which === d.DOWN && b.currentValue) return void b.suggest();
            if (!b.disabled && b.visible) {
                switch (a.which) {
                    case d.ESC:
                        b.el.val(b.currentValue), b.hide();
                        break;
                    case d.RIGHT:
                        if (b.hint && b.options.onHint && b.isCursorAtEnd()) {
                            b.selectHint();
                            break
                        }
                        return;
                    case d.TAB:
                        if (b.hint && b.options.onHint) return void b.selectHint();
                        if (-1 === b.selectedIndex) return void b.hide();
                        if (b.select(b.selectedIndex), b.options.tabDisabled === !1) return;
                        break;
                    case d.RETURN:
                        if (-1 === b.selectedIndex) return void b.hide();
                        b.select(b.selectedIndex);
                        break;
                    case d.UP:
                        b.moveUp();
                        break;
                    case d.DOWN:
                        b.moveDown();
                        break;
                    default:
                        return
                }
                a.stopImmediatePropagation(), a.preventDefault()
            }
        }, onKeyUp: function (a) {
            var b = this;
            if (!b.disabled) {
                switch (a.which) {
                    case d.UP:
                    case d.DOWN:
                        return
                }
                clearInterval(b.onChangeInterval), b.currentValue !== b.el.val() && (b.findBestHint(), b.options.deferRequestBy > 0 ? b.onChangeInterval = setInterval(function () {
                    b.onValueChange()
                }, b.options.deferRequestBy) : b.onValueChange())
            }
        }, onValueChange: function () {
            var b, c = this, d = c.options, e = c.el.val(), f = c.getQuery(e);
            return c.selection && c.currentValue !== f && (c.selection = null, (d.onInvalidateSelection || a.noop).call(c.element)), clearInterval(c.onChangeInterval), c.currentValue = e, c.selectedIndex = -1, d.triggerSelectOnValidInput && (b = c.findSuggestionIndex(f), -1 !== b) ? void c.select(b) : void (f.length < d.minChars ? c.hide() : c.getSuggestions(f))
        }, findSuggestionIndex: function (b) {
            var c = this, d = -1, e = b.toLowerCase();
            return a.each(c.suggestions, function (a, b) {
                return b.value.toLowerCase() === e ? (d = a, !1) : void 0
            }), d
        }, getQuery: function (b) {
            var c, d = this.options.delimiter;
            return d ? (c = b.split(d), a.trim(c[c.length - 1])) : b
        }, getSuggestionsLocal: function (b) {
            var c, d = this, e = d.options, f = b.toLowerCase(), g = e.lookupFilter, h = parseInt(e.lookupLimit, 10);
            return c = {
                suggestions: a.grep(e.lookup, function (a) {
                    return g(a, b, f)
                })
            }, h && c.suggestions.length > h && (c.suggestions = c.suggestions.slice(0, h)), c
        }, getSuggestions: function (b) {
            var c, d, e, f, g = this, h = g.options, i = h.serviceUrl;
            if (h.params[h.paramName] = b, d = h.ignoreParams ? null : h.params, h.onSearchStart.call(g.element, h.params) !== !1) {
                if (a.isFunction(h.lookup)) return void h.lookup(b, function (a) {
                    g.suggestions = a.suggestions, g.suggest(), h.onSearchComplete.call(g.element, b, a.suggestions)
                });
                g.isLocal ? c = g.getSuggestionsLocal(b) : (a.isFunction(i) && (i = i.call(g.element, b)), e = i + "?" + a.param(d || {}), c = g.cachedResponse[e]), c && a.isArray(c.suggestions) ? (g.suggestions = c.suggestions, g.suggest(), h.onSearchComplete.call(g.element, b, c.suggestions)) : g.isBadQuery(b) ? h.onSearchComplete.call(g.element, b, []) : (g.currentRequest && g.currentRequest.abort(), f = {
                    url: i,
                    data: d,
                    type: h.type,
                    dataType: h.dataType
                }, a.extend(f, h.ajaxSettings), g.currentRequest = a.ajax(f).done(function (a) {
                    var c;
                    g.currentRequest = null, c = h.transformResult(a), g.processResponse(c, b, e), h.onSearchComplete.call(g.element, b, c.suggestions)
                }).fail(function (a, c, d) {
                    h.onSearchError.call(g.element, b, a, c, d)
                }))
            }
        }, isBadQuery: function (a) {
            if (!this.options.preventBadQueries) return !1;
            for (var b = this.badQueries, c = b.length; c--;) if (0 === a.indexOf(b[c])) return !0;
            return !1
        }, hide: function () {
            var b = this, c = a(b.suggestionsContainer);
            a.isFunction(b.options.onHide) && b.visible && b.options.onHide.call(b.element, c), b.visible = !1, b.selectedIndex = -1, clearInterval(b.onChangeInterval), a(b.suggestionsContainer).hide(), b.signalHint(null)
        }, suggest: function () {
            if (0 === this.suggestions.length) return void (this.options.showNoSuggestionNotice ? this.noSuggestions() : this.hide());
            var b, c, d = this, e = d.options, f = e.groupBy, g = e.formatResult, h = d.getQuery(d.currentValue),
                i = d.classes.suggestion, j = d.classes.selected, k = a(d.suggestionsContainer),
                l = a(d.noSuggestionsContainer), m = e.beforeRender, n = "", o = function (a, c) {
                    var d = a.data[f];
                    return b === d ? "" : (b = d, '<div class="autocomplete-group"><strong>' + b + "</strong></div>")
                };
            return e.triggerSelectOnValidInput && (c = d.findSuggestionIndex(h), -1 !== c) ? void d.select(c) : (a.each(d.suggestions, function (a, b) {
                f && (n += o(b, h, a)), n += '<div class="' + i + '" data-index="' + a + '">' + g(b, h) + "</div>"
            }), this.adjustContainerWidth(), l.detach(), k.html(n), a.isFunction(m) && m.call(d.element, k), d.fixPosition(), k.show(), e.autoSelectFirst && (d.selectedIndex = 0, k.scrollTop(0), k.children("." + i).first().addClass(j)), d.visible = !0, void d.findBestHint())
        }, noSuggestions: function () {
            var b = this, c = a(b.suggestionsContainer), d = a(b.noSuggestionsContainer);
            this.adjustContainerWidth(), d.detach(), c.empty(), c.append(d), b.fixPosition(), c.show(), b.visible = !0
        }, adjustContainerWidth: function () {
            var b, c = this, d = c.options, e = a(c.suggestionsContainer);
            "auto" === d.width && (b = c.el.outerWidth() - 2, e.width(b > 0 ? b : 300))
        }, findBestHint: function () {
            var b = this, c = b.el.val().toLowerCase(), d = null;
            c && (a.each(b.suggestions, function (a, b) {
                var e = 0 === b.value.toLowerCase().indexOf(c);
                return e && (d = b), !e
            }), b.signalHint(d))
        }, signalHint: function (b) {
            var c = "", d = this;
            b && (c = d.currentValue + b.value.substr(d.currentValue.length)), d.hintValue !== c && (d.hintValue = c, d.hint = b, (this.options.onHint || a.noop)(c))
        }, verifySuggestionsFormat: function (b) {
            return b.length && "string" == typeof b[0] ? a.map(b, function (a) {
                return {value: a, data: null}
            }) : b
        }, validateOrientation: function (b, c) {
            return b = a.trim(b || "").toLowerCase(), -1 === a.inArray(b, ["auto", "bottom", "top"]) && (b = c), b
        }, processResponse: function (a, b, c) {
            var d = this, e = d.options;
            a.suggestions = d.verifySuggestionsFormat(a.suggestions), e.noCache || (d.cachedResponse[c] = a, e.preventBadQueries && 0 === a.suggestions.length && d.badQueries.push(b)), b === d.getQuery(d.currentValue) && (d.suggestions = a.suggestions, d.suggest())
        }, activate: function (b) {
            var c, d = this, e = d.classes.selected, f = a(d.suggestionsContainer),
                g = f.find("." + d.classes.suggestion);
            return f.find("." + e).removeClass(e), d.selectedIndex = b, -1 !== d.selectedIndex && g.length > d.selectedIndex ? (c = g.get(d.selectedIndex), a(c).addClass(e), c) : null
        }, selectHint: function () {
            var b = this, c = a.inArray(b.hint, b.suggestions);
            b.select(c)
        }, select: function (a) {
            var b = this;
            b.hide(), b.onSelect(a)
        }, moveUp: function () {
            var b = this;
            if (-1 !== b.selectedIndex) return 0 === b.selectedIndex ? (a(b.suggestionsContainer).children().first().removeClass(b.classes.selected), b.selectedIndex = -1, b.el.val(b.currentValue), void b.findBestHint()) : void b.adjustScroll(b.selectedIndex - 1)
        }, moveDown: function () {
            var a = this;
            a.selectedIndex !== a.suggestions.length - 1 && a.adjustScroll(a.selectedIndex + 1)
        }, adjustScroll: function (b) {
            var c = this, d = c.activate(b);
            if (d) {
                var e, f, g, h = a(d).outerHeight();
                e = d.offsetTop, f = a(c.suggestionsContainer).scrollTop(), g = f + c.options.maxHeight - h, f > e ? a(c.suggestionsContainer).scrollTop(e) : e > g && a(c.suggestionsContainer).scrollTop(e - c.options.maxHeight + h), c.options.preserveInput || c.el.val(c.getValue(c.suggestions[b].value)), c.signalHint(null)
            }
        }, onSelect: function (b) {
            var c = this, d = c.options.onSelect, e = c.suggestions[b];
            c.currentValue = c.getValue(e.value), c.currentValue === c.el.val() || c.options.preserveInput || c.el.val(c.currentValue), c.signalHint(null), c.suggestions = [], c.selection = e, a.isFunction(d) && d.call(c.element, e)
        }, getValue: function (a) {
            var b, c, d = this, e = d.options.delimiter;
            return e ? (b = d.currentValue, c = b.split(e), 1 === c.length ? a : b.substr(0, b.length - c[c.length - 1].length) + a) : a
        }, dispose: function () {
            var b = this;
            b.el.off(".autocomplete").removeData("autocomplete"), b.disableKillerFn(), a(window).off("resize.autocomplete", b.fixPositionCapture), a(b.suggestionsContainer).remove()
        }
    }, a.fn.autocomplete = a.fn.devbridgeAutocomplete = function (c, d) {
        var e = "autocomplete";
        return 0 === arguments.length ? this.first().data(e) : this.each(function () {
            var f = a(this), g = f.data(e);
            "string" == typeof c ? g && "function" == typeof g[c] && g[c](d) : (g && g.dispose && g.dispose(), g = new b(this, c), f.data(e, g))
        })
    }
}), function (a) {
    a.reject = function (d) {
        var e = a.extend(!0, {
            reject: {all: !1, msie: 6},
            display: [],
            browserShow: !0,
            browserInfo: {
                chrome: {text: "Google Chrome", url: "http://www.google.com/chrome/"},
                firefox: {text: "Mozilla Firefox", url: "http://www.mozilla.com/firefox/"},
                safari: {text: "Safari", url: "http://www.apple.com/safari/download/"},
                opera: {text: "Opera", url: "http://www.opera.com/download/"},
                msie: {text: "Internet Explorer", url: "http://www.microsoft.com/windows/Internet-explorer/"}
            },
            header: "Did you know that your Internet Browser is out of date?",
            paragraph1: "Your browser is out of date, and may not be compatible with our website. A list of the most popular web browsers can be found below.",
            paragraph2: "Just click on the icons to get to the download page",
            close: !0,
            closeMessage: "By closing this window you acknowledge that your experience on this website may be degraded",
            closeLink: "Close This Window",
            closeURL: "#",
            closeESC: !0,
            closeCookie: !1,
            cookieSettings: {path: "/", expires: 0},
            imagePath: "./images/",
            overlayBgColor: "#000",
            overlayOpacity: .8,
            fadeInTime: "fast",
            fadeOutTime: "fast",
            analytics: !1
        }, d);
        e.display.length < 1 && (e.display = ["chrome", "firefox", "safari", "opera", "msie"]), a.isFunction(e.beforeReject) && e.beforeReject(), e.close || (e.closeESC = !1);
        var f = function (b) {
            var c = b[a.layout.name], d = b[a.browser.name];
            return !!(b.all || d && (d === !0 || a.browser.versionNumber <= d) || b[a.browser.className] || c && (c === !0 || a.layout.versionNumber <= c) || b[a.os.name])
        };
        if (!f(e.reject)) return a.isFunction(e.onFail) && e.onFail(), !1;
        if (e.close && e.closeCookie) {
            var g = "jreject-close", h = function (b, c) {
                if ("undefined" != typeof c) {
                    var d = "";
                    if (0 !== e.cookieSettings.expires) {
                        var f = new Date;
                        f.setTime(f.getTime() + 1e3 * e.cookieSettings.expires), d = "; expires=" + f.toGMTString()
                    }
                    var g = e.cookieSettings.path || "/";
                    return document.cookie = b + "=" + encodeURIComponent(c ? c : "") + d + "; path=" + g, !0
                }
                var h, i = null;
                if (document.cookie && "" !== document.cookie) for (var j = document.cookie.split(";"), k = j.length, l = 0; k > l; ++l) if (h = a.trim(j[l]), h.substring(0, b.length + 1) == b + "=") {
                    var m = b.length;
                    i = decodeURIComponent(h.substring(m + 1));
                    break
                }
                return i
            };
            if (h(g)) return !1
        }
        var i = '<div id="jr_overlay"></div><div id="jr_wrap"><div id="jr_inner"><h1 id="jr_header">' + e.header + "</h1>" + ("" === e.paragraph1 ? "" : "<p>" + e.paragraph1 + "</p>") + ("" === e.paragraph2 ? "" : "<p>" + e.paragraph2 + "</p>"),
            j = 0;
        if (e.browserShow) {
            i += "<ul>";
            for (var k in e.display) {
                var l = e.display[k], m = e.browserInfo[l] || !1;
                if (m && (void 0 == m.allow || f(m.allow))) {
                    var n = m.url || "#";
                    i += '<li id="jr_' + l + '"><div class="jr_icon"></div><div><a href="' + n + '">' + (m.text || "Unknown") + "</a></div></li>", ++j
                }
            }
            i += "</ul>"
        }
        i += '<div id="jr_close">' + (e.close ? '<a href="' + e.closeURL + '">' + e.closeLink + "</a><p>" + e.closeMessage + "</p>" : "") + "</div></div></div>";
        var o = a("<div>" + i + "</div>"), p = b(), q = c();
        o.bind("closejr", function () {
            if (!e.close) return !1;
            a.isFunction(e.beforeClose) && e.beforeClose(), a(this).unbind("closejr"), a("#jr_overlay,#jr_wrap").fadeOut(e.fadeOutTime, function () {
                a(this).remove(), a.isFunction(e.afterClose) && e.afterClose()
            });
            var b = "embed.jr_hidden, object.jr_hidden, select.jr_hidden, applet.jr_hidden";
            return a(b).show().removeClass("jr_hidden"), e.closeCookie && h(g, "true"), !0
        });
        var r = function (a) {
            if (!e.analytics) return !1;
            var b = a.split(/\/+/g)[1];
            try {
                ga("send", "event", "External", "Click", b, a)
            } catch (c) {
                try {
                    _gaq.push(["_trackEvent", "External Links", b, a])
                } catch (c) {
                }
            }
        }, s = function (a) {
            return r(a), window.open(a, "jr_" + Math.round(11 * Math.random())), !1
        };
        return o.find("#jr_overlay").css({
            width: p[0],
            height: p[1],
            background: e.overlayBgColor,
            opacity: e.overlayOpacity
        }), o.find("#jr_wrap").css({top: q[1] + p[3] / 4, left: q[0]}), o.find("#jr_inner").css({
            minWidth: 100 * j,
            maxWidth: 140 * j,
            width: "trident" == a.layout.name ? 155 * j : "auto"
        }), o.find("#jr_inner li").css({background: 'transparent url("' + e.imagePath + 'background_browser.gif")no-repeat scroll left top'}), o.find("#jr_inner li .jr_icon").each(function () {
            var b = a(this);
            b.css("background", "transparent url(" + e.imagePath + "browser_" + b.parent("li").attr("id").replace(/jr_/, "") + ".gif) no-repeat scroll left top"), b.click(function () {
                var b = a(this).next("div").children("a").attr("href");
                s(b)
            })
        }), o.find("#jr_inner li a").click(function () {
            return s(a(this).attr("href")), !1
        }), o.find("#jr_close a").click(function () {
            return a(this).trigger("closejr"), "#" === e.closeURL ? !1 : void 0
        }), a("#jr_overlay").focus(), a("embed, object, select, applet").each(function () {
            a(this).is(":visible") && a(this).hide().addClass("jr_hidden")
        }), a("body").append(o.hide().fadeIn(e.fadeInTime)), a(window).bind("resize scroll", function () {
            var d = b();
            a("#jr_overlay").css({width: d[0], height: d[1]});
            var e = c();
            a("#jr_wrap").css({top: e[1] + d[3] / 4, left: e[0]})
        }), e.closeESC && a(document).bind("keydown", function (a) {
            27 == a.keyCode && o.trigger("closejr")
        }), a.isFunction(e.afterReject) && e.afterReject(), !0
    };
    var b = function () {
        var a = window.innerWidth && window.scrollMaxX ? window.innerWidth + window.scrollMaxX : document.body.scrollWidth > document.body.offsetWidth ? document.body.scrollWidth : document.body.offsetWidth,
            b = window.innerHeight && window.scrollMaxY ? window.innerHeight + window.scrollMaxY : document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight,
            c = window.innerWidth ? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth,
            d = window.innerHeight ? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
        return [c > a ? a : c, d > b ? d : b, c, d]
    }, c = function () {
        return [window.pageXOffset ? window.pageXOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollLeft : document.body.scrollLeft, window.pageYOffset ? window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop]
    }
}(jQuery), function (a) {
    a.browserTest = function (b, c) {
        var d = "unknown", e = "X", f = function (a, b) {
            for (var c = 0; c < b.length; c += 1) a = a.replace(b[c][0], b[c][1]);
            return a
        }, g = function (b, c, g, h) {
            var i = {name: f((c.exec(b) || [d, d])[1], g)};
            if (i[i.name] = !0, i.opera ? i.version = window.opera.version() : i.version = (h.exec(b) || [e, e, e, e])[3], /safari/.test(i.name)) {
                var j = /(safari)(\/|\s)([a-z0-9\.\+]*?)(\;|dev|rel|\s|$)/, k = j.exec(b);
                k && k[3] && k[3] < 400 && (i.version = "2.0")
            } else "presto" === i.name && (i.version = a.browser.version > 9.27 ? "futhark" : "linear_b");
            if (/msie/.test(i.name) && i.version === e) {
                var l = /rv:(\d+\.\d+)/.exec(b);
                i.version = l[1]
            }
            i.versionNumber = parseFloat(i.version, 10) || 0;
            var m = 1;
            return i.versionNumber < 100 && i.versionNumber > 9 && (m = 2), i.versionX = i.version !== e ? i.version.substr(0, m) : e, i.className = i.name + i.versionX, i
        };
        b = (/Opera|Navigator|Minefield|KHTML|Chrome|CriOS/.test(b) ? f(b, [[/(Firefox|MSIE|KHTML,\slike\sGecko|Konqueror)/, ""], ["Chrome Safari", "Chrome"], ["CriOS", "Chrome"], ["KHTML", "Konqueror"], ["Minefield", "Firefox"], ["Navigator", "Netscape"]]) : b).toLowerCase(), a.browser = a.extend(c ? {} : a.browser, g(b, /(camino|chrome|crios|firefox|netscape|konqueror|lynx|msie|trident|opera|safari)/, [["trident", "msie"]], /(camino|chrome|crios|firefox|netscape|netscape6|opera|version|konqueror|lynx|msie|rv|safari)(:|\/|\s)([a-z0-9\.\+]*?)(\;|dev|rel|\s|$)/)), a.layout = g(b, /(gecko|konqueror|msie|trident|opera|webkit)/, [["konqueror", "khtml"], ["msie", "trident"], ["opera", "presto"]], /(applewebkit|rv|konqueror|msie)(\:|\/|\s)([a-z0-9\.]*?)(\;|\)|\s)/), a.os = {name: (/(win|mac|linux|sunos|solaris|iphone|ipad)/.exec(navigator.platform.toLowerCase()) || [d])[0].replace("sunos", "solaris")}, c || a("html").addClass([a.os.name, a.browser.name, a.browser.className, a.layout.name, a.layout.className].join(" "))
    }, a.browserTest(navigator.userAgent)
}(jQuery), +function (a) {
    "use strict";

    function b(b) {
        var c = b.attr("data-target");
        c || (c = b.attr("href"), c = c && /#[A-Za-z]/.test(c) && c.replace(/.*(?=#[^\s]*$)/, ""));
        var d = c && a(c);
        return d && d.length ? d : b.parent()
    }

    function c(c) {
        c && 3 === c.which || (a(e).remove(), a(f).each(function () {
            var d = a(this), e = b(d), f = {relatedTarget: this};
            e.hasClass("open") && (c && "click" == c.type && /input|textarea/i.test(c.target.tagName) && a.contains(e[0], c.target) || (e.trigger(c = a.Event("hide.bs.dropdown", f)), c.isDefaultPrevented() || (d.attr("aria-expanded", "false"), e.removeClass("open").trigger("hidden.bs.dropdown", f))))
        }))
    }

    function d(b) {
        return this.each(function () {
            var c = a(this), d = c.data("bs.dropdown");
            d || c.data("bs.dropdown", d = new g(this)), "string" == typeof b && d[b].call(c)
        })
    }

    var e = ".dropdown-backdrop", f = '[data-toggle="dropdown"]', g = function (b) {
        a(b).on("click.bs.dropdown", this.toggle)
    };
    g.VERSION = "3.3.4", g.prototype.toggle = function (d) {
        var e = a(this);
        if (!e.is(".disabled, :disabled")) {
            var f = b(e), g = f.hasClass("open");
            if (c(), !g) {
                "ontouchstart" in document.documentElement && !f.closest(".navbar-nav").length && a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click", c);
                var h = {relatedTarget: this};
                if (f.trigger(d = a.Event("show.bs.dropdown", h)), d.isDefaultPrevented()) return;
                e.trigger("focus").attr("aria-expanded", "true"), f.toggleClass("open").trigger("shown.bs.dropdown", h)
            }
            return !1
        }
    }, g.prototype.keydown = function (c) {
        if (/(38|40|27|32)/.test(c.which) && !/input|textarea/i.test(c.target.tagName)) {
            var d = a(this);
            if (c.preventDefault(), c.stopPropagation(), !d.is(".disabled, :disabled")) {
                var e = b(d), g = e.hasClass("open");
                if (!g && 27 != c.which || g && 27 == c.which) return 27 == c.which && e.find(f).trigger("focus"), d.trigger("click");
                var h = " li:not(.disabled):visible a", i = e.find('[role="menu"]' + h + ', [role="listbox"]' + h);
                if (i.length) {
                    var j = i.index(c.target);
                    38 == c.which && j > 0 && j--, 40 == c.which && j < i.length - 1 && j++, ~j || (j = 0), i.eq(j).trigger("focus")
                }
            }
        }
    };
    var h = a.fn.dropdown;
    a.fn.dropdown = d, a.fn.dropdown.Constructor = g, a.fn.dropdown.noConflict = function () {
        return a.fn.dropdown = h, this
    }, a(document).on("click.bs.dropdown.data-api", c).on("click.bs.dropdown.data-api", ".dropdown form", function (a) {
        a.stopPropagation()
    }).on("click.bs.dropdown.data-api", f, g.prototype.toggle).on("keydown.bs.dropdown.data-api", f, g.prototype.keydown).on("keydown.bs.dropdown.data-api", ".dropdown-menu", g.prototype.keydown)
}(jQuery), +function (a) {
    "use strict";

    function b(b) {
        return this.each(function () {
            var d = a(this), e = d.data("bs.tab");
            e || d.data("bs.tab", e = new c(this)), "string" == typeof b && e[b]()
        })
    }

    var c = function (b) {
        this.element = a(b)
    };
    c.VERSION = "3.3.4", c.TRANSITION_DURATION = 150, c.prototype.show = function () {
        var b = this.element, c = b.closest("ul:not(.dropdown-menu)"), d = b.data("target");
        if (d || (d = b.attr("href"), d = d && d.replace(/.*(?=#[^\s]*$)/, "")), !b.parent("li").hasClass("active")) {
            var e = c.find(".active:last a"), f = a.Event("hide.bs.tab", {relatedTarget: b[0]}),
                g = a.Event("show.bs.tab", {relatedTarget: e[0]});
            if (e.trigger(f), b.trigger(g), !g.isDefaultPrevented() && !f.isDefaultPrevented()) {
                var h = a(d);
                this.activate(b.closest("li"), c), this.activate(h, h.parent(), function () {
                    e.trigger({type: "hidden.bs.tab", relatedTarget: b[0]}), b.trigger({
                        type: "shown.bs.tab",
                        relatedTarget: e[0]
                    })
                })
            }
        }
    }, c.prototype.activate = function (b, d, e) {
        function f() {
            g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !1), b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded", !0), h ? (b[0].offsetWidth, b.addClass("in")) : b.removeClass("fade"), b.parent(".dropdown-menu").length && b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !0), e && e()
        }

        var g = d.find("> .active"),
            h = e && a.support.transition && (g.length && g.hasClass("fade") || !!d.find("> .fade").length);
        g.length && h ? g.one("bsTransitionEnd", f).emulateTransitionEnd(c.TRANSITION_DURATION) : f(), g.removeClass("in")
    };
    var d = a.fn.tab;
    a.fn.tab = b, a.fn.tab.Constructor = c, a.fn.tab.noConflict = function () {
        return a.fn.tab = d, this
    };
    var e = function (c) {
        c.preventDefault(), b.call(a(this), "show")
    };
    a(document).on("click.bs.tab.data-api", '[data-toggle="tab"]', e).on("click.bs.tab.data-api", '[data-toggle="pill"]', e)
}(jQuery), +function (a) {
    "use strict";

    function b(b, d) {
        return this.each(function () {
            var e = a(this), f = e.data("bs.modal"), g = a.extend({}, c.DEFAULTS, e.data(), "object" == typeof b && b);
            f || e.data("bs.modal", f = new c(this, g)), "string" == typeof b ? f[b](d) : g.show && f.show(d)
        })
    }

    var c = function (b, c) {
        this.options = c, this.$body = a(document.body), this.$element = a(b), this.$dialog = this.$element.find(".modal-dialog"), this.$backdrop = null, this.isShown = null, this.originalBodyPad = null, this.scrollbarWidth = 0, this.ignoreBackdropClick = !1, this.options.remote && this.$element.find(".modal-content").load(this.options.remote, a.proxy(function () {
            this.$element.trigger("loaded.bs.modal")
        }, this))
    };
    c.VERSION = "3.3.4", c.TRANSITION_DURATION = 300, c.BACKDROP_TRANSITION_DURATION = 150, c.DEFAULTS = {
        backdrop: !0,
        keyboard: !0,
        show: !0
    }, c.prototype.toggle = function (a) {
        return this.isShown ? this.hide() : this.show(a)
    }, c.prototype.show = function (b) {
        var d = this, e = a.Event("show.bs.modal", {relatedTarget: b});
        this.$element.trigger(e), this.isShown || e.isDefaultPrevented() || (this.isShown = !0, this.checkScrollbar(), this.setScrollbar(), this.$body.addClass("modal-open"), this.escape(), this.resize(), this.$element.on("click.dismiss.bs.modal", '[data-dismiss="modal"]', a.proxy(this.hide, this)), this.$dialog.on("mousedown.dismiss.bs.modal", function () {
            d.$element.one("mouseup.dismiss.bs.modal", function (b) {
                a(b.target).is(d.$element) && (d.ignoreBackdropClick = !0)
            })
        }), this.backdrop(function () {
            var e = a.support.transition && d.$element.hasClass("fade");
            d.$element.parent().length || d.$element.appendTo(d.$body), d.$element.show().scrollTop(0), d.adjustDialog(), e && d.$element[0].offsetWidth, d.$element.addClass("in"), d.enforceFocus();
            var f = a.Event("shown.bs.modal", {relatedTarget: b});
            e ? d.$dialog.one("bsTransitionEnd", function () {
                d.$element.trigger("focus").trigger(f)
            }).emulateTransitionEnd(c.TRANSITION_DURATION) : d.$element.trigger("focus").trigger(f)
        }))
    }, c.prototype.hide = function (b) {
        b && b.preventDefault(), b = a.Event("hide.bs.modal"), this.$element.trigger(b), this.isShown && !b.isDefaultPrevented() && (this.isShown = !1, this.escape(), this.resize(), a(document).off("focusin.bs.modal"), this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"), this.$dialog.off("mousedown.dismiss.bs.modal"), a.support.transition && this.$element.hasClass("fade") ? this.$element.one("bsTransitionEnd", a.proxy(this.hideModal, this)).emulateTransitionEnd(c.TRANSITION_DURATION) : this.hideModal())
    }, c.prototype.enforceFocus = function () {
        a(document).off("focusin.bs.modal").on("focusin.bs.modal", a.proxy(function (a) {
            this.$element[0] === a.target || this.$element.has(a.target).length || this.$element.trigger("focus")
        }, this))
    }, c.prototype.escape = function () {
        this.isShown && this.options.keyboard ? this.$element.on("keydown.dismiss.bs.modal", a.proxy(function (a) {
            27 == a.which && this.hide()
        }, this)) : this.isShown || this.$element.off("keydown.dismiss.bs.modal")
    }, c.prototype.resize = function () {
        this.isShown ? a(window).on("resize.bs.modal", a.proxy(this.handleUpdate, this)) : a(window).off("resize.bs.modal")
    }, c.prototype.hideModal = function () {
        var a = this;
        this.$element.hide(), this.backdrop(function () {
            a.$body.removeClass("modal-open"), a.resetAdjustments(), a.resetScrollbar(), a.$element.trigger("hidden.bs.modal")
        })
    }, c.prototype.removeBackdrop = function () {
        this.$backdrop && this.$backdrop.remove(), this.$backdrop = null
    }, c.prototype.backdrop = function (b) {
        var d = this, e = this.$element.hasClass("fade") ? "fade" : "";
        if (this.isShown && this.options.backdrop) {
            var f = a.support.transition && e;
            if (this.$backdrop = a(document.createElement("div")).addClass("modal-backdrop " + e).appendTo(this.$body), this.$element.on("click.dismiss.bs.modal", a.proxy(function (a) {
                return this.ignoreBackdropClick ? void (this.ignoreBackdropClick = !1) : void (a.target === a.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus() : this.hide()))
            }, this)), f && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !b) return;
            f ? this.$backdrop.one("bsTransitionEnd", b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION) : b()
        } else if (!this.isShown && this.$backdrop) {
            this.$backdrop.removeClass("in");
            var g = function () {
                d.removeBackdrop(), b && b()
            };
            a.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION) : g()
        } else b && b()
    }, c.prototype.handleUpdate = function () {
        this.adjustDialog()
    }, c.prototype.adjustDialog = function () {
        var a = this.$element[0].scrollHeight > document.documentElement.clientHeight;
        this.$element.css({
            paddingLeft: !this.bodyIsOverflowing && a ? this.scrollbarWidth : "",
            paddingRight: this.bodyIsOverflowing && !a ? this.scrollbarWidth : ""
        })
    }, c.prototype.resetAdjustments = function () {
        this.$element.css({paddingLeft: "", paddingRight: ""})
    }, c.prototype.checkScrollbar = function () {
        var a = window.innerWidth;
        if (!a) {
            var b = document.documentElement.getBoundingClientRect();
            a = b.right - Math.abs(b.left)
        }
        this.bodyIsOverflowing = document.body.clientWidth < a, this.scrollbarWidth = this.measureScrollbar()
    }, c.prototype.setScrollbar = function () {
        var a = parseInt(this.$body.css("padding-right") || 0, 10);
        this.originalBodyPad = document.body.style.paddingRight || "", this.bodyIsOverflowing && this.$body.css("padding-right", a + this.scrollbarWidth)
    }, c.prototype.resetScrollbar = function () {
        this.$body.css("padding-right", this.originalBodyPad)
    }, c.prototype.measureScrollbar = function () {
        var a = document.createElement("div");
        a.className = "modal-scrollbar-measure", this.$body.append(a);
        var b = a.offsetWidth - a.clientWidth;
        return this.$body[0].removeChild(a), b
    };
    var d = a.fn.modal;
    a.fn.modal = b, a.fn.modal.Constructor = c, a.fn.modal.noConflict = function () {
        return a.fn.modal = d, this
    }, a(document).on("click.bs.modal.data-api", '[data-toggle="modal"]', function (c) {
        var d = a(this), e = d.attr("href"), f = a(d.attr("data-target") || e && e.replace(/.*(?=#[^\s]+$)/, "")),
            g = f.data("bs.modal") ? "toggle" : a.extend({remote: !/#/.test(e) && e}, f.data(), d.data());
        d.is("a") && c.preventDefault(), f.one("show.bs.modal", function (a) {
            a.isDefaultPrevented() || f.one("hidden.bs.modal", function () {
                d.is(":visible") && d.trigger("focus")
            })
        }), b.call(f, g, this)
    })
}(jQuery), !function (a) {
    "use strict";

    function b(b) {
        var c = [{re: /[\xC0-\xC6]/g, ch: "A"}, {re: /[\xE0-\xE6]/g, ch: "a"}, {
            re: /[\xC8-\xCB]/g,
            ch: "E"
        }, {re: /[\xE8-\xEB]/g, ch: "e"}, {re: /[\xCC-\xCF]/g, ch: "I"}, {
            re: /[\xEC-\xEF]/g,
            ch: "i"
        }, {re: /[\xD2-\xD6]/g, ch: "O"}, {re: /[\xF2-\xF6]/g, ch: "o"}, {
            re: /[\xD9-\xDC]/g,
            ch: "U"
        }, {re: /[\xF9-\xFC]/g, ch: "u"}, {re: /[\xC7-\xE7]/g, ch: "c"}, {re: /[\xD1]/g, ch: "N"}, {
            re: /[\xF1]/g,
            ch: "n"
        }];
        return a.each(c, function () {
            b = b.replace(this.re, this.ch)
        }), b
    }

    function c(a) {
        var b = {"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#x27;", "`": "&#x60;"},
            c = "(?:" + Object.keys(b).join("|") + ")", d = new RegExp(c), e = new RegExp(c, "g"),
            f = null == a ? "" : "" + a;
        return d.test(f) ? f.replace(e, function (a) {
            return b[a]
        }) : f
    }

    function d(b, c) {
        var d = arguments, f = b, g = c;
        [].shift.apply(d);
        var h, i = this.each(function () {
            var b = a(this);
            if (b.is("select")) {
                var c = b.data("selectpicker"), i = "object" == typeof f && f;
                if (c) {
                    if (i) for (var j in i) i.hasOwnProperty(j) && (c.options[j] = i[j])
                } else {
                    var k = a.extend({}, e.DEFAULTS, a.fn.selectpicker.defaults || {}, b.data(), i);
                    b.data("selectpicker", c = new e(this, k, g))
                }
                "string" == typeof f && (h = c[f] instanceof Function ? c[f].apply(c, d) : c.options[f])
            }
        });
        return "undefined" != typeof h ? h : i
    }

    String.prototype.includes || !function () {
        var a = {}.toString, b = function () {
            try {
                var a = {}, b = Object.defineProperty, c = b(a, a, a) && b
            } catch (d) {
            }
            return c
        }(), c = "".indexOf, d = function (b) {
            if (null == this) throw TypeError();
            var d = String(this);
            if (b && "[object RegExp]" == a.call(b)) throw TypeError();
            var e = d.length, f = String(b), g = f.length, h = arguments.length > 1 ? arguments[1] : void 0,
                i = h ? Number(h) : 0;
            i != i && (i = 0);
            var j = Math.min(Math.max(i, 0), e);
            return g + j > e ? !1 : -1 != c.call(d, f, i)
        };
        b ? b(String.prototype, "includes", {value: d, configurable: !0, writable: !0}) : String.prototype.includes = d
    }(), String.prototype.startsWith || !function () {
        var a = function () {
            try {
                var a = {}, b = Object.defineProperty, c = b(a, a, a) && b
            } catch (d) {
            }
            return c
        }(), b = {}.toString, c = function (a) {
            if (null == this) throw TypeError();
            var c = String(this);
            if (a && "[object RegExp]" == b.call(a)) throw TypeError();
            var d = c.length, e = String(a), f = e.length, g = arguments.length > 1 ? arguments[1] : void 0,
                h = g ? Number(g) : 0;
            h != h && (h = 0);
            var i = Math.min(Math.max(h, 0), d);
            if (f + i > d) return !1;
            for (var j = -1; ++j < f;) if (c.charCodeAt(i + j) != e.charCodeAt(j)) return !1;
            return !0
        };
        a ? a(String.prototype, "startsWith", {
            value: c,
            configurable: !0,
            writable: !0
        }) : String.prototype.startsWith = c
    }(), Object.keys || (Object.keys = function (a, b, c) {
        c = [];
        for (b in a) c.hasOwnProperty.call(a, b) && c.push(b);
        return c
    }), a.expr[":"].icontains = function (b, c, d) {
        var e = a(b), f = (e.data("tokens") || e.text()).toUpperCase();
        return f.includes(d[3].toUpperCase())
    }, a.expr[":"].ibegins = function (b, c, d) {
        var e = a(b), f = (e.data("tokens") || e.text()).toUpperCase();
        return f.startsWith(d[3].toUpperCase())
    }, a.expr[":"].aicontains = function (b, c, d) {
        var e = a(b), f = (e.data("tokens") || e.data("normalizedText") || e.text()).toUpperCase();
        return f.includes(d[3].toUpperCase())
    }, a.expr[":"].aibegins = function (b, c, d) {
        var e = a(b), f = (e.data("tokens") || e.data("normalizedText") || e.text()).toUpperCase();
        return f.startsWith(d[3].toUpperCase())
    };
    var e = function (b, c, d) {
        d && (d.stopPropagation(), d.preventDefault()), this.$element = a(b), this.$newElement = null, this.$button = null, this.$menu = null, this.$lis = null, this.options = c, null === this.options.title && (this.options.title = this.$element.attr("title")), this.val = e.prototype.val, this.render = e.prototype.render, this.refresh = e.prototype.refresh, this.setStyle = e.prototype.setStyle, this.selectAll = e.prototype.selectAll, this.deselectAll = e.prototype.deselectAll, this.destroy = e.prototype.remove, this.remove = e.prototype.remove, this.show = e.prototype.show, this.hide = e.prototype.hide, this.init()
    };
    e.VERSION = "1.7.1", e.DEFAULTS = {
        noneSelectedText: "Nothing selected",
        noneResultsText: "No results matched {0}",
        countSelectedText: function (a, b) {
            return 1 == a ? "{0} item selected" : "{0} items selected"
        },
        maxOptionsText: function (a, b) {
            return [1 == a ? "Limit reached ({n} item max)" : "Limit reached ({n} items max)", 1 == b ? "Group limit reached ({n} item max)" : "Group limit reached ({n} items max)"]
        },
        selectAllText: "Select All",
        deselectAllText: "Deselect All",
        doneButton: !1,
        doneButtonText: "Close",
        multipleSeparator: ", ",
        styleBase: "btn",
        style: "btn-default",
        size: "auto",
        title: null,
        selectedTextFormat: "values",
        width: !1,
        container: !1,
        hideDisabled: !1,
        showSubtext: !1,
        showIcon: !0,
        showContent: !0,
        dropupAuto: !0,
        header: !1,
        liveSearch: !1,
        liveSearchPlaceholder: null,
        liveSearchNormalize: !1,
        liveSearchStyle: "contains",
        actionsBox: !1,
        iconBase: "glyphicon",
        tickIcon: "glyphicon-ok",
        maxOptions: !1,
        mobile: !1,
        selectOnTab: !1,
        dropdownAlignRight: !1
    }, e.prototype = {
        constructor: e, init: function () {
            var b = this, c = this.$element.attr("id");
            this.$element.addClass("bs-select-hidden"), this.liObj = {}, this.multiple = this.$element.prop("multiple"), this.autofocus = this.$element.prop("autofocus"), this.$newElement = this.createView(), this.$element.after(this.$newElement), this.$button = this.$newElement.children("button"), this.$menu = this.$newElement.children(".dropdown-menu"), this.$menuInner = this.$menu.children(".inner"), this.$searchbox = this.$menu.find("input"), this.options.dropdownAlignRight && this.$menu.addClass("dropdown-menu-right"), "undefined" != typeof c && (this.$button.attr("data-id", c), a('label[for="' + c + '"]').click(function (a) {
                a.preventDefault(), b.$button.focus()
            })), this.checkDisabled(), this.clickListener(), this.options.liveSearch && this.liveSearchListener(), this.render(), this.setStyle(), this.setWidth(), this.options.container && this.selectPosition(), this.$menu.data("this", this), this.$newElement.data("this", this), this.options.mobile && this.mobile(), this.$newElement.on("hide.bs.dropdown", function (a) {
                b.$element.trigger("hide.bs.select", a)
            }), this.$newElement.on("hidden.bs.dropdown", function (a) {
                b.$element.trigger("hidden.bs.select", a)
            }), this.$newElement.on("show.bs.dropdown", function (a) {
                b.$element.trigger("show.bs.select", a)
            }), this.$newElement.on("shown.bs.dropdown", function (a) {
                b.$element.trigger("shown.bs.select", a)
            }), setTimeout(function () {
                b.$element.trigger("loaded.bs.select")
            })
        }, createDropdown: function () {
            var b = this.multiple ? " show-tick" : "",
                d = this.$element.parent().hasClass("input-group") ? " input-group-btn" : "",
                e = this.autofocus ? " autofocus" : "",
                f = this.options.header ? '<div class="popover-title"><button type="button" class="close" aria-hidden="true">&times;</button>' + this.options.header + "</div>" : "",
                g = this.options.liveSearch ? '<div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"' + (null === this.options.liveSearchPlaceholder ? "" : ' placeholder="' + c(this.options.liveSearchPlaceholder) + '"') + "></div>" : "",
                h = this.multiple && this.options.actionsBox ? '<div class="bs-actionsbox"><div class="btn-group btn-group-sm btn-block"><button type="button" class="actions-btn bs-select-all btn btn-default">' + this.options.selectAllText + '</button><button type="button" class="actions-btn bs-deselect-all btn btn-default">' + this.options.deselectAllText + "</button></div></div>" : "",
                i = this.multiple && this.options.doneButton ? '<div class="bs-donebutton"><div class="btn-group btn-block"><button type="button" class="btn btn-sm btn-default">' + this.options.doneButtonText + "</button></div></div>" : "",
                j = '<div class="btn-group bootstrap-select' + b + d + '"><button type="button" class="' + this.options.styleBase + ' dropdown-toggle" data-toggle="dropdown"' + e + '><span class="filter-option pull-left"></span>&nbsp;<span class="caret"></span></button><div class="dropdown-menu open">' + f + g + h + '<ul class="dropdown-menu inner" role="menu"></ul>' + i + "</div></div>";
            return a(j)
        }, createView: function () {
            var a = this.createDropdown(), b = this.createLi();
            return a.find("ul")[0].innerHTML = b, a
        }, reloadLi: function () {
            this.destroyLi();
            var a = this.createLi();
            this.$menuInner[0].innerHTML = a
        }, destroyLi: function () {
            this.$menu.find("li").remove()
        }, createLi: function () {
            var d = this, e = [], f = 0, g = document.createElement("option"), h = -1, i = function (a, b, c, d) {
                return "<li" + ("undefined" != typeof c & "" !== c ? ' class="' + c + '"' : "") + ("undefined" != typeof b & null !== b ? ' data-original-index="' + b + '"' : "") + ("undefined" != typeof d & null !== d ? 'data-optgroup="' + d + '"' : "") + ">" + a + "</li>"
            }, j = function (a, e, f, g) {
                return '<a tabindex="0"' + ("undefined" != typeof e ? ' class="' + e + '"' : "") + ("undefined" != typeof f ? ' style="' + f + '"' : "") + (d.options.liveSearchNormalize ? ' data-normalized-text="' + b(c(a)) + '"' : "") + ("undefined" != typeof g || null !== g ? ' data-tokens="' + g + '"' : "") + ">" + a + '<span class="' + d.options.iconBase + " " + d.options.tickIcon + ' check-mark"></span></a>'
            };
            if (this.options.title && !this.multiple && !this.$element.find(".bs-title-option").length) {
                h--;
                var k = this.$element[0];
                g.className = "bs-title-option", g.appendChild(document.createTextNode(this.options.title)), g.value = "", k.insertBefore(g, k.firstChild), null === k.options[k.selectedIndex].getAttribute("selected") && (g.selected = !0)
            }
            return this.$element.find("option").each(function (b) {
                var c = a(this);
                if (h++, !c.hasClass("bs-title-option")) {
                    var g = this.className || "", k = this.style.cssText,
                        l = c.data("content") ? c.data("content") : c.html(),
                        m = c.data("tokens") ? c.data("tokens") : null,
                        n = "undefined" != typeof c.data("subtext") ? '<small class="text-muted">' + c.data("subtext") + "</small>" : "",
                        o = "undefined" != typeof c.data("icon") ? '<span class="' + d.options.iconBase + " " + c.data("icon") + '"></span> ' : "",
                        p = this.disabled || "OPTGROUP" === this.parentElement.tagName && this.parentElement.disabled;
                    if ("" !== o && p && (o = "<span>" + o + "</span>"), !d.options.hideDisabled || !p) {
                        if (c.data("content") || (l = o + '<span class="text">' + l + n + "</span>"), "OPTGROUP" === this.parentElement.tagName && c.data("divider") !== !0) {
                            if (0 === c.index()) {
                                f += 1;
                                var q = this.parentElement.label,
                                    r = "undefined" != typeof c.parent().data("subtext") ? '<small class="text-muted">' + c.parent().data("subtext") + "</small>" : "",
                                    s = c.parent().data("icon") ? '<span class="' + d.options.iconBase + " " + c.parent().data("icon") + '"></span> ' : "",
                                    t = " " + this.parentElement.className || "";
                                q = s + '<span class="text">' + q + r + "</span>", 0 !== b && e.length > 0 && (h++, e.push(i("", null, "divider", f + "div"))), h++, e.push(i(q, null, "dropdown-header" + t, f))
                            }
                            e.push(i(j(l, "opt " + g + t, k, m), b, "", f))
                        } else c.data("divider") === !0 ? e.push(i("", b, "divider")) : c.data("hidden") === !0 ? e.push(i(j(l, g, k, m), b, "hidden is-hidden")) : (this.previousElementSibling && "OPTGROUP" === this.previousElementSibling.tagName && (h++, e.push(i("", null, "divider", f + "div"))), e.push(i(j(l, g, k, m), b)));
                        d.liObj[b] = h
                    }
                }
            }), this.multiple || 0 !== this.$element.find("option:selected").length || this.options.title || this.$element.find("option").eq(0).prop("selected", !0).attr("selected", "selected"), e.join("")
        }, findLis: function () {
            return null == this.$lis && (this.$lis = this.$menu.find("li")), this.$lis
        }, render: function (b) {
            var c, d = this;
            b !== !1 && this.$element.find("option").each(function (a) {
                var b = d.findLis().eq(d.liObj[a]);
                d.setDisabled(a, this.disabled || "OPTGROUP" === this.parentElement.tagName && this.parentElement.disabled, b), d.setSelected(a, this.selected, b)
            }), this.tabIndex();
            var e = this.$element.find("option").map(function () {
                if (this.selected) {
                    if (d.options.hideDisabled && (this.disabled || "OPTGROUP" === this.parentElement.tagName && this.parentElement.disabled)) return !1;
                    var b, c = a(this),
                        e = c.data("icon") && d.options.showIcon ? '<i class="' + d.options.iconBase + " " + c.data("icon") + '"></i> ' : "";
                    return b = d.options.showSubtext && c.data("subtext") && !d.multiple ? ' <small class="text-muted">' + c.data("subtext") + "</small>" : "", "undefined" != typeof c.attr("title") ? c.attr("title") : c.data("content") && d.options.showContent ? c.data("content") : e + c.html() + b
                }
            }).toArray(), f = this.multiple ? e.join(this.options.multipleSeparator) : e[0];
            if (this.multiple && this.options.selectedTextFormat.indexOf("count") > -1) {
                var g = this.options.selectedTextFormat.split(">");
                if (g.length > 1 && e.length > g[1] || 1 == g.length && e.length >= 2) {
                    c = this.options.hideDisabled ? ", [disabled]" : "";
                    var h = this.$element.find("option").not('[data-divider="true"], [data-hidden="true"]' + c).length,
                        i = "function" == typeof this.options.countSelectedText ? this.options.countSelectedText(e.length, h) : this.options.countSelectedText;
                    f = i.replace("{0}", e.length.toString()).replace("{1}", h.toString())
                }
            }
            void 0 == this.options.title && (this.options.title = this.$element.attr("title")), "static" == this.options.selectedTextFormat && (f = this.options.title), f || (f = "undefined" != typeof this.options.title ? this.options.title : this.options.noneSelectedText), this.$button.attr("title", a.trim(f.replace(/<[^>]*>?/g, ""))), this.$button.children(".filter-option").html(f), this.$element.trigger("rendered.bs.select")
        }, setStyle: function (a, b) {
            this.$element.attr("class") && this.$newElement.addClass(this.$element.attr("class").replace(/selectpicker|mobile-device|bs-select-hidden|validate\[.*\]/gi, ""));
            var c = a ? a : this.options.style;
            "add" == b ? this.$button.addClass(c) : "remove" == b ? this.$button.removeClass(c) : (this.$button.removeClass(this.options.style), this.$button.addClass(c))
        }, liHeight: function (b) {
            if (b || this.options.size !== !1 && !this.sizeInfo) {
                var c = document.createElement("div"), d = document.createElement("div"),
                    e = document.createElement("ul"), f = document.createElement("li"),
                    g = document.createElement("li"), h = document.createElement("a"),
                    i = document.createElement("span"),
                    j = this.options.header ? this.$menu.find(".popover-title")[0].cloneNode(!0) : null,
                    k = this.options.liveSearch ? document.createElement("div") : null,
                    l = this.options.actionsBox && this.multiple ? this.$menu.find(".bs-actionsbox")[0].cloneNode(!0) : null,
                    m = this.options.doneButton && this.multiple ? this.$menu.find(".bs-donebutton")[0].cloneNode(!0) : null;
                if (i.className = "text", c.className = this.$menu[0].parentNode.className + " open", d.className = "dropdown-menu open", e.className = "dropdown-menu inner", f.className = "divider", i.appendChild(document.createTextNode("Inner text")), h.appendChild(i), g.appendChild(h), e.appendChild(g), e.appendChild(f), j && d.appendChild(j), k) {
                    var n = document.createElement("span");
                    k.className = "bs-searchbox", n.className = "form-control", k.appendChild(n), d.appendChild(k)
                }
                l && d.appendChild(l), d.appendChild(e), m && d.appendChild(m), c.appendChild(d), document.body.appendChild(c);
                var o = h.offsetHeight, p = j ? j.offsetHeight : 0, q = k ? k.offsetHeight : 0,
                    r = l ? l.offsetHeight : 0, s = m ? m.offsetHeight : 0, t = a(f).outerHeight(!0),
                    u = getComputedStyle ? getComputedStyle(d) : !1, v = u ? a(d) : null,
                    w = parseInt(u ? u.paddingTop : v.css("paddingTop")) + parseInt(u ? u.paddingBottom : v.css("paddingBottom")) + parseInt(u ? u.borderTopWidth : v.css("borderTopWidth")) + parseInt(u ? u.borderBottomWidth : v.css("borderBottomWidth")),
                    x = w + parseInt(u ? u.marginTop : v.css("marginTop")) + parseInt(u ? u.marginBottom : v.css("marginBottom")) + 2;
                document.body.removeChild(c), this.sizeInfo = {
                    liHeight: o,
                    headerHeight: p,
                    searchHeight: q,
                    actionsHeight: r,
                    doneButtonHeight: s,
                    dividerHeight: t,
                    menuPadding: w,
                    menuExtras: x
                }
            }
        }, setSize: function () {
            this.findLis(), this.liHeight();
            var b, c, d, e, f = this, g = this.$menu, h = this.$menuInner, i = a(window),
                j = this.$newElement[0].offsetHeight, k = this.sizeInfo.liHeight, l = this.sizeInfo.headerHeight,
                m = this.sizeInfo.searchHeight, n = this.sizeInfo.actionsHeight, o = this.sizeInfo.doneButtonHeight,
                p = this.sizeInfo.dividerHeight, q = this.sizeInfo.menuPadding, r = this.sizeInfo.menuExtras,
                s = this.options.hideDisabled ? ".disabled" : "", t = function () {
                    d = f.$newElement.offset().top - i.scrollTop(), e = i.height() - d - j
                };
            if (t(), this.options.header && g.css("padding-top", 0), "auto" === this.options.size) {
                var u = function () {
                    var i, j = function (b, c) {
                            return function (d) {
                                return c ? d.classList ? d.classList.contains(b) : a(d).hasClass(b) : !(d.classList ? d.classList.contains(b) : a(d).hasClass(b))
                            }
                        }, p = f.$menuInner[0].getElementsByTagName("li"),
                        s = Array.prototype.filter ? Array.prototype.filter.call(p, j("hidden", !1)) : f.$lis.not(".hidden"),
                        u = Array.prototype.filter ? Array.prototype.filter.call(s, j("dropdown-header", !0)) : s.filter(".dropdown-header");
                    t(), b = e - r, f.options.container ? (g.data("height") || g.data("height", g.height()), c = g.data("height")) : c = g.height(), f.options.dropupAuto && f.$newElement.toggleClass("dropup", d > e && c > b - r), f.$newElement.hasClass("dropup") && (b = d - r), i = s.length + u.length > 3 ? 3 * k + r - 2 : 0, g.css({
                        "max-height": b + "px",
                        overflow: "hidden",
                        "min-height": i + l + m + n + o + "px"
                    }), h.css({
                        "max-height": b - l - m - n - o - q + "px",
                        "overflow-y": "auto",
                        "min-height": Math.max(i - q, 0) + "px"
                    })
                };
                u(), this.$searchbox.off("input.getSize propertychange.getSize").on("input.getSize propertychange.getSize", u), i.off("resize.getSize scroll.getSize").on("resize.getSize scroll.getSize", u)
            } else if (this.options.size && "auto" != this.options.size && this.$lis.not(s).length > this.options.size) {
                var v = this.$lis.not(".divider").not(s).children().slice(0, this.options.size).last().parent().index(),
                    w = this.$lis.slice(0, v + 1).filter(".divider").length;
                b = k * this.options.size + w * p + q, f.options.container ? (g.data("height") || g.data("height", g.height()), c = g.data("height")) : c = g.height(), f.options.dropupAuto && this.$newElement.toggleClass("dropup", d > e && c > b - r), g.css({
                    "max-height": b + l + m + n + o + "px",
                    overflow: "hidden",
                    "min-height": ""
                }), h.css({"max-height": b - q + "px", "overflow-y": "auto", "min-height": ""})
            }
        }, setWidth: function () {
            if ("auto" === this.options.width) {
                this.$menu.css("min-width", "0");
                var a = this.$menu.parent().clone().appendTo("body"),
                    b = this.options.container ? this.$newElement.clone().appendTo("body") : a,
                    c = a.children(".dropdown-menu").outerWidth(),
                    d = b.css("width", "auto").children("button").outerWidth();
                a.remove(), b.remove(), this.$newElement.css("width", Math.max(c, d) + "px")
            } else "fit" === this.options.width ? (this.$menu.css("min-width", ""), this.$newElement.css("width", "").addClass("fit-width")) : this.options.width ? (this.$menu.css("min-width", ""), this.$newElement.css("width", this.options.width)) : (this.$menu.css("min-width", ""), this.$newElement.css("width", ""));
            this.$newElement.hasClass("fit-width") && "fit" !== this.options.width && this.$newElement.removeClass("fit-width")
        }, selectPosition: function () {
            var b, c, d = this, e = "<div />", f = a(e), g = function (a) {
                f.addClass(a.attr("class").replace(/form-control|fit-width/gi, "")).toggleClass("dropup", a.hasClass("dropup")), b = a.offset(), c = a.hasClass("dropup") ? 0 : a[0].offsetHeight, f.css({
                    top: b.top + c,
                    left: b.left,
                    width: a[0].offsetWidth,
                    position: "absolute"
                })
            };
            this.$newElement.on("click", function () {
                d.isDisabled() || (g(a(this)), f.appendTo(d.options.container), f.toggleClass("open", !a(this).hasClass("open")), f.append(d.$menu))
            }), a(window).on("resize scroll", function () {
                g(d.$newElement)
            }), this.$element.on("hide.bs.select", function () {
                d.$menu.data("height", d.$menu.height()), f.detach()
            })
        }, setSelected: function (a, b, c) {
            if (!c) var c = this.findLis().eq(this.liObj[a]);
            c.toggleClass("selected", b)
        }, setDisabled: function (a, b, c) {
            if (!c) var c = this.findLis().eq(this.liObj[a]);
            b ? c.addClass("disabled").children("a").attr("href", "#").attr("tabindex", -1) : c.removeClass("disabled").children("a").removeAttr("href").attr("tabindex", 0)
        }, isDisabled: function () {
            return this.$element[0].disabled
        }, checkDisabled: function () {
            var a = this;
            this.isDisabled() ? (this.$newElement.addClass("disabled"), this.$button.addClass("disabled").attr("tabindex", -1)) : (this.$button.hasClass("disabled") && (this.$newElement.removeClass("disabled"), this.$button.removeClass("disabled")), -1 != this.$button.attr("tabindex") || this.$element.data("tabindex") || this.$button.removeAttr("tabindex")), this.$button.click(function () {
                return !a.isDisabled()
            })
        }, tabIndex: function () {
            this.$element.is("[tabindex]") && (this.$element.data("tabindex", this.$element.attr("tabindex")), this.$button.attr("tabindex", this.$element.data("tabindex")))
        }, clickListener: function () {
            var b = this, c = a(document);
            this.$newElement.on("touchstart.dropdown", ".dropdown-menu", function (a) {
                a.stopPropagation()
            }), c.data("spaceSelect", !1), this.$button.on("keyup", function (a) {
                /(32)/.test(a.keyCode.toString(10)) && c.data("spaceSelect") && (a.preventDefault(), c.data("spaceSelect", !1))
            }), this.$newElement.on("click", function () {
                b.setSize(), b.$element.on("shown.bs.select", function () {
                    if (b.options.liveSearch || b.multiple) {
                        if (!b.multiple) {
                            var a = b.liObj[b.$element[0].selectedIndex];
                            if ("number" != typeof a) return;
                            var c = b.$lis.eq(a)[0].offsetTop - b.$menuInner[0].offsetTop;
                            c = c - b.$menuInner[0].offsetHeight / 2 + b.sizeInfo.liHeight / 2, b.$menuInner[0].scrollTop = c
                        }
                    } else b.$menu.find(".selected a").focus()
                })
            }), this.$menu.on("click", "li a", function (c) {
                var d = a(this), e = d.parent().data("originalIndex"), f = b.$element.val(),
                    g = b.$element.prop("selectedIndex");
                if (b.multiple && c.stopPropagation(), c.preventDefault(), !b.isDisabled() && !d.parent().hasClass("disabled")) {
                    var h = b.$element.find("option"), i = h.eq(e), j = i.prop("selected"), k = i.parent("optgroup"),
                        l = b.options.maxOptions, m = k.data("maxOptions") || !1;
                    if (b.multiple) {
                        if (i.prop("selected", !j), b.setSelected(e, !j), d.blur(), l !== !1 || m !== !1) {
                            var n = l < h.filter(":selected").length, o = m < k.find("option:selected").length;
                            if (l && n || m && o) if (l && 1 == l) h.prop("selected", !1), i.prop("selected", !0), b.$menu.find(".selected").removeClass("selected"), b.setSelected(e, !0); else if (m && 1 == m) {
                                k.find("option:selected").prop("selected", !1), i.prop("selected", !0);
                                var p = d.parent().data("optgroup");
                                b.$menu.find('[data-optgroup="' + p + '"]').removeClass("selected"), b.setSelected(e, !0)
                            } else {
                                var q = "function" == typeof b.options.maxOptionsText ? b.options.maxOptionsText(l, m) : b.options.maxOptionsText,
                                    r = q[0].replace("{n}", l), s = q[1].replace("{n}", m),
                                    t = a('<div class="notify"></div>');
                                q[2] && (r = r.replace("{var}", q[2][l > 1 ? 0 : 1]), s = s.replace("{var}", q[2][m > 1 ? 0 : 1])), i.prop("selected", !1), b.$menu.append(t), l && n && (t.append(a("<div>" + r + "</div>")), b.$element.trigger("maxReached.bs.select")), m && o && (t.append(a("<div>" + s + "</div>")), b.$element.trigger("maxReachedGrp.bs.select")), setTimeout(function () {
                                    b.setSelected(e, !1)
                                }, 10), t.delay(750).fadeOut(300, function () {
                                    a(this).remove()
                                })
                            }
                        }
                    } else h.prop("selected", !1), i.prop("selected", !0), b.$menu.find(".selected").removeClass("selected"), b.setSelected(e, !0);
                    b.multiple ? b.options.liveSearch && b.$searchbox.focus() : b.$button.focus(), (f != b.$element.val() && b.multiple || g != b.$element.prop("selectedIndex") && !b.multiple) && (b.$element.change(), b.$element.trigger("changed.bs.select", [e, i.prop("selected"), j]))
                }
            }), this.$menu.on("click", "li.disabled a, .popover-title, .popover-title :not(.close)", function (c) {
                c.currentTarget == this && (c.preventDefault(), c.stopPropagation(), b.options.liveSearch && !a(c.target).hasClass("close") ? b.$searchbox.focus() : b.$button.focus())
            }), this.$menu.on("click", "li.divider, li.dropdown-header", function (a) {
                a.preventDefault(), a.stopPropagation(), b.options.liveSearch ? b.$searchbox.focus() : b.$button.focus()
            }), this.$menu.on("click", ".popover-title .close", function () {
                b.$button.click()
            }), this.$searchbox.on("click", function (a) {
                a.stopPropagation()
            }), this.$menu.on("click", ".actions-btn", function (c) {
                b.options.liveSearch ? b.$searchbox.focus() : b.$button.focus(), c.preventDefault(), c.stopPropagation(), a(this).hasClass("bs-select-all") ? b.selectAll() : b.deselectAll(), b.$element.change()
            }), this.$element.change(function () {
                b.render(!1)
            })
        }, liveSearchListener: function () {
            var d = this, e = a('<li class="no-results"></li>');
            this.$newElement.on("click.dropdown.data-api touchstart.dropdown.data-api", function () {
                d.$menuInner.find(".active").removeClass("active"), d.$searchbox.val() && (d.$searchbox.val(""), d.$lis.not(".is-hidden").removeClass("hidden"), e.parent().length && e.remove()), d.multiple || d.$menuInner.find(".selected").addClass("active"), setTimeout(function () {
                    d.$searchbox.focus()
                }, 10)
            }), this.$searchbox.on("click.dropdown.data-api focus.dropdown.data-api touchend.dropdown.data-api", function (a) {
                a.stopPropagation()
            }), this.$searchbox.on("input propertychange", function () {
                if (d.$searchbox.val()) {
                    var f = d.$lis.not(".is-hidden").removeClass("hidden").children("a");
                    f = d.options.liveSearchNormalize ? f.not(":a" + d._searchStyle() + "(" + b(d.$searchbox.val()) + ")") : f.not(":" + d._searchStyle() + "(" + d.$searchbox.val() + ")"), f.parent().addClass("hidden"), d.$lis.filter(".dropdown-header").each(function () {
                        var b = a(this), c = b.data("optgroup");
                        0 === d.$lis.filter("[data-optgroup=" + c + "]").not(b).not(".hidden").length && (b.addClass("hidden"), d.$lis.filter("[data-optgroup=" + c + "div]").addClass("hidden"))
                    });
                    var g = d.$lis.not(".hidden");
                    g.each(function (b) {
                        var c = a(this);
                        c.hasClass("divider") && (c.index() === g.eq(0).index() || c.index() === g.last().index() || g.eq(b + 1).hasClass("divider")) && c.addClass("hidden")
                    }), d.$lis.not(".hidden, .no-results").length ? e.parent().length && e.remove() : (e.parent().length && e.remove(), e.html(d.options.noneResultsText.replace("{0}", '"' + c(d.$searchbox.val()) + '"')).show(), d.$menuInner.append(e))
                } else d.$lis.not(".is-hidden").removeClass("hidden"), e.parent().length && e.remove();
                d.$lis.filter(".active").removeClass("active"), d.$lis.not(".hidden, .divider, .dropdown-header").eq(0).addClass("active").children("a").focus(), a(this).focus()
            })
        }, _searchStyle: function () {
            var a = "icontains";
            switch (this.options.liveSearchStyle) {
                case"begins":
                case"startsWith":
                    a = "ibegins";
                    break;
                case"contains":
            }
            return a
        }, val: function (a) {
            return "undefined" != typeof a ? (this.$element.val(a), this.render(), this.$element) : this.$element.val()
        }, selectAll: function () {
            this.findLis(), this.$element.find("option:enabled").not("[data-divider], [data-hidden]").prop("selected", !0), this.$lis.not(".divider, .dropdown-header, .disabled, .hidden").addClass("selected"), this.render(!1)
        }, deselectAll: function () {
            this.findLis(), this.$element.find("option:enabled").not("[data-divider], [data-hidden]").prop("selected", !1), this.$lis.not(".divider, .dropdown-header, .disabled, .hidden").removeClass("selected"), this.render(!1)
        }, keydown: function (c) {
            var d, e, f, g, h, i, j, k, l, m = a(this), n = m.is("input") ? m.parent().parent() : m.parent(),
                o = n.data("this"), p = ":not(.disabled, .hidden, .dropdown-header, .divider)", q = {
                    32: " ",
                    48: "0",
                    49: "1",
                    50: "2",
                    51: "3",
                    52: "4",
                    53: "5",
                    54: "6",
                    55: "7",
                    56: "8",
                    57: "9",
                    59: ";",
                    65: "a",
                    66: "b",
                    67: "c",
                    68: "d",
                    69: "e",
                    70: "f",
                    71: "g",
                    72: "h",
                    73: "i",
                    74: "j",
                    75: "k",
                    76: "l",
                    77: "m",
                    78: "n",
                    79: "o",
                    80: "p",
                    81: "q",
                    82: "r",
                    83: "s",
                    84: "t",
                    85: "u",
                    86: "v",
                    87: "w",
                    88: "x",
                    89: "y",
                    90: "z",
                    96: "0",
                    97: "1",
                    98: "2",
                    99: "3",
                    100: "4",
                    101: "5",
                    102: "6",
                    103: "7",
                    104: "8",
                    105: "9"
                };
            if (o.options.liveSearch && (n = m.parent().parent()), o.options.container && (n = o.$menu), d = a("[role=menu] li a", n), l = o.$menu.parent().hasClass("open"), !l && (c.keyCode >= 48 && c.keyCode <= 57 || event.keyCode >= 65 && event.keyCode <= 90) && (o.options.container ? o.$newElement.trigger("click") : (o.setSize(), o.$menu.parent().addClass("open"), l = !0), o.$searchbox.focus()), o.options.liveSearch && (/(^9$|27)/.test(c.keyCode.toString(10)) && l && 0 === o.$menu.find(".active").length && (c.preventDefault(), o.$menu.parent().removeClass("open"), o.options.container && o.$newElement.removeClass("open"), o.$button.focus()), d = a("[role=menu] li:not(.disabled, .hidden, .dropdown-header, .divider)", n), m.val() || /(38|40)/.test(c.keyCode.toString(10)) || 0 === d.filter(".active").length && (d = o.$newElement.find("li"), d = o.options.liveSearchNormalize ? d.filter(":a" + o._searchStyle() + "(" + b(q[c.keyCode]) + ")") : d.filter(":" + o._searchStyle() + "(" + q[c.keyCode] + ")"))), d.length) {
                if (/(38|40)/.test(c.keyCode.toString(10))) e = d.index(d.filter(":focus")), g = d.parent(p).first().data("originalIndex"), h = d.parent(p).last().data("originalIndex"), f = d.eq(e).parent().nextAll(p).eq(0).data("originalIndex"), i = d.eq(e).parent().prevAll(p).eq(0).data("originalIndex"), j = d.eq(f).parent().prevAll(p).eq(0).data("originalIndex"), o.options.liveSearch && (d.each(function (b) {
                    a(this).hasClass("disabled") || a(this).data("index", b)
                }), e = d.index(d.filter(".active")), g = d.first().data("index"), h = d.last().data("index"), f = d.eq(e).nextAll().eq(0).data("index"), i = d.eq(e).prevAll().eq(0).data("index"), j = d.eq(f).prevAll().eq(0).data("index")), k = m.data("prevIndex"), 38 == c.keyCode ? (o.options.liveSearch && (e -= 1), e != j && e > i && (e = i), g > e && (e = g), e == k && (e = h)) : 40 == c.keyCode && (o.options.liveSearch && (e += 1), -1 == e && (e = 0), e != j && f > e && (e = f), e > h && (e = h), e == k && (e = g)), m.data("prevIndex", e), o.options.liveSearch ? (c.preventDefault(), m.hasClass("dropdown-toggle") || (d.removeClass("active").eq(e).addClass("active").children("a").focus(), m.focus())) : d.eq(e).focus(); else if (!m.is("input")) {
                    var r, s, t = [];
                    d.each(function () {
                        a(this).parent().hasClass("disabled") || a.trim(a(this).text().toLowerCase()).substring(0, 1) == q[c.keyCode] && t.push(a(this).parent().index())
                    }), r = a(document).data("keycount"), r++, a(document).data("keycount", r), s = a.trim(a(":focus").text().toLowerCase()).substring(0, 1), s != q[c.keyCode] ? (r = 1, a(document).data("keycount", r)) : r >= t.length && (a(document).data("keycount", 0), r > t.length && (r = 1)), d.eq(t[r - 1]).focus()
                }
                if ((/(13|32)/.test(c.keyCode.toString(10)) || /(^9$)/.test(c.keyCode.toString(10)) && o.options.selectOnTab) && l) {
                    if (/(32)/.test(c.keyCode.toString(10)) || c.preventDefault(), o.options.liveSearch) /(32)/.test(c.keyCode.toString(10)) || (o.$menu.find(".active a").click(), m.focus()); else {
                        var u = a(":focus");
                        u.click(), u.focus(), c.preventDefault(), a(document).data("spaceSelect", !0)
                    }
                    a(document).data("keycount", 0)
                }
                (/(^9$|27)/.test(c.keyCode.toString(10)) && l && (o.multiple || o.options.liveSearch) || /(27)/.test(c.keyCode.toString(10)) && !l) && (o.$menu.parent().removeClass("open"), o.options.container && o.$newElement.removeClass("open"), o.$button.focus())
            }
        }, mobile: function () {
            this.$element.addClass("mobile-device").appendTo(this.$newElement), this.options.container && this.$menu.hide()
        }, refresh: function () {
            this.$lis = null, this.reloadLi(), this.render(), this.checkDisabled(), this.liHeight(!0), this.setStyle(), this.setWidth(), this.$searchbox.trigger("propertychange"), this.$element.trigger("refreshed.bs.select")
        }, hide: function () {
            this.$newElement.hide()
        }, show: function () {
            this.$newElement.show()
        }, remove: function () {
            this.$newElement.remove(), this.$element.remove()
        }
    };
    var f = a.fn.selectpicker;
    a.fn.selectpicker = d, a.fn.selectpicker.Constructor = e, a.fn.selectpicker.noConflict = function () {
        return a.fn.selectpicker = f, this
    }, a(document).data("keycount", 0).on("keydown", '.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="menu"], .bs-searchbox input', e.prototype.keydown).on("focusin.modal", '.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="menu"], .bs-searchbox input', function (a) {
        a.stopPropagation()
    }), a(window).on("load.bs.select.data-api", function () {
        a(".selectpicker").each(function () {
            var b = a(this);
            d.call(b, b.data())
        })
    })
}(jQuery), function (a) {
    "function" == typeof define && define.amd ? define("pnotify", ["jquery"], a) : a(jQuery)
}(function (a) {
    var b, c, d = {dir1: "down", dir2: "left", push: "bottom", spacing1: 25, spacing2: 25, context: a("body")},
        e = a(window), f = function () {
            c = a("body"), PNotify.prototype.options.stack.context = c, e = a(window), e.bind("resize", function () {
                b && clearTimeout(b), b = setTimeout(function () {
                    PNotify.positionAll(!0)
                }, 10)
            })
        };
    return PNotify = function (a) {
        this.parseOptions(a), this.init()
    }, a.extend(PNotify.prototype, {
        version: "2.0.1",
        options: {
            title: !1,
            title_escape: !1,
            text: !1,
            text_escape: !1,
            styling: "bootstrap3",
            addclass: "",
            cornerclass: "",
            auto_display: !0,
            width: "300px",
            min_height: "16px",
            type: "notice",
            icon: !0,
            opacity: 1,
            animation: "fade",
            animate_speed: "slow",
            position_animate_speed: 500,
            shadow: !0,
            hide: !0,
            delay: 8e3,
            mouse_reset: !0,
            remove: !0,
            insert_brs: !0,
            destroy: !0,
            stack: d
        },
        modules: {},
        runModules: function (a, b) {
            var c, d;
            for (d in this.modules) c = "object" == typeof b && d in b ? b[d] : b, "function" == typeof this.modules[d][a] && this.modules[d][a](this, "object" == typeof this.options[d] ? this.options[d] : {}, c)
        },
        state: "initializing",
        timer: null,
        styles: null,
        elem: null,
        container: null,
        title_container: null,
        text_container: null,
        animating: !1,
        timerHide: !1,
        init: function () {
            var b = this;
            return this.modules = {}, a.extend(!0, this.modules, PNotify.prototype.modules), this.styles = "object" == typeof this.options.styling ? this.options.styling : PNotify.styling[this.options.styling], this.elem = a("<div />", {
                "class": "ui-pnotify " + this.options.addclass,
                css: {display: "none"},
                mouseenter: function (a) {
                    if (b.options.mouse_reset && "out" === b.animating) {
                        if (!b.timerHide) return;
                        b.cancelRemove()
                    }
                    b.options.hide && b.options.mouse_reset && b.cancelRemove()
                },
                mouseleave: function (a) {
                    b.options.hide && b.options.mouse_reset && b.queueRemove(), PNotify.positionAll()
                }
            }), this.container = a("<div />", {"class": this.styles.container + " ui-pnotify-container " + ("error" === this.options.type ? this.styles.error : "info" === this.options.type ? this.styles.info : "success" === this.options.type ? this.styles.success : this.styles.notice)}).appendTo(this.elem), "" !== this.options.cornerclass && this.container.removeClass("ui-corner-all").addClass(this.options.cornerclass), this.options.shadow && this.container.addClass("ui-pnotify-shadow"), !1 !== this.options.icon && a("<div />", {"class": "ui-pnotify-icon"}).append(a("<span />", {"class": !0 === this.options.icon ? "error" === this.options.type ? this.styles.error_icon : "info" === this.options.type ? this.styles.info_icon : "success" === this.options.type ? this.styles.success_icon : this.styles.notice_icon : this.options.icon})).prependTo(this.container), this.title_container = a("<h4 />", {"class": "ui-pnotify-title"}).appendTo(this.container), !1 === this.options.title ? this.title_container.hide() : this.options.title_escape ? this.title_container.text(this.options.title) : this.title_container.html(this.options.title), this.text_container = a("<div />", {"class": "ui-pnotify-text"}).appendTo(this.container), !1 === this.options.text ? this.text_container.hide() : this.options.text_escape ? this.text_container.text(this.options.text) : this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text), "string" == typeof this.options.width && this.elem.css("width", this.options.width), "string" == typeof this.options.min_height && this.container.css("min-height", this.options.min_height), PNotify.notices = "top" === this.options.stack.push ? a.merge([this], PNotify.notices) : a.merge(PNotify.notices, [this]), "top" === this.options.stack.push && this.queuePosition(!1, 1), this.options.stack.animation = !1, this.runModules("init"), this.options.auto_display && this.open(), this
        },
        update: function (b) {
            var c = this.options;
            return this.parseOptions(c, b), this.options.cornerclass !== c.cornerclass && this.container.removeClass("ui-corner-all " + c.cornerclass).addClass(this.options.cornerclass), this.options.shadow !== c.shadow && (this.options.shadow ? this.container.addClass("ui-pnotify-shadow") : this.container.removeClass("ui-pnotify-shadow")), !1 === this.options.addclass ? this.elem.removeClass(c.addclass) : this.options.addclass !== c.addclass && this.elem.removeClass(c.addclass).addClass(this.options.addclass), !1 === this.options.title ? this.title_container.slideUp("fast") : this.options.title !== c.title && (this.options.title_escape ? this.title_container.text(this.options.title) : this.title_container.html(this.options.title), !1 === c.title && this.title_container.slideDown(200)), !1 === this.options.text ? this.text_container.slideUp("fast") : this.options.text !== c.text && (this.options.text_escape ? this.text_container.text(this.options.text) : this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text), !1 === c.text && this.text_container.slideDown(200)), this.options.type !== c.type && this.container.removeClass(this.styles.error + " " + this.styles.notice + " " + this.styles.success + " " + this.styles.info).addClass("error" === this.options.type ? this.styles.error : "info" === this.options.type ? this.styles.info : "success" === this.options.type ? this.styles.success : this.styles.notice), (this.options.icon !== c.icon || !0 === this.options.icon && this.options.type !== c.type) && (this.container.find("div.ui-pnotify-icon").remove(), !1 !== this.options.icon && a("<div />", {"class": "ui-pnotify-icon"}).append(a("<span />", {"class": !0 === this.options.icon ? "error" === this.options.type ? this.styles.error_icon : "info" === this.options.type ? this.styles.info_icon : "success" === this.options.type ? this.styles.success_icon : this.styles.notice_icon : this.options.icon})).prependTo(this.container)), this.options.width !== c.width && this.elem.animate({width: this.options.width}), this.options.min_height !== c.min_height && this.container.animate({minHeight: this.options.min_height}), this.options.opacity !== c.opacity && this.elem.fadeTo(this.options.animate_speed, this.options.opacity), this.options.hide ? c.hide || this.queueRemove() : this.cancelRemove(), this.queuePosition(!0), this.runModules("update", c), this
        },
        open: function () {
            this.state = "opening", this.runModules("beforeOpen");
            var a = this;
            return this.elem.parent().length || this.elem.appendTo(this.options.stack.context ? this.options.stack.context : c), "top" !== this.options.stack.push && this.position(!0), "fade" === this.options.animation || "fade" === this.options.animation.effect_in ? this.elem.show().fadeTo(0, 0).hide() : 1 !== this.options.opacity && this.elem.show().fadeTo(0, this.options.opacity).hide(), this.animateIn(function () {
                a.queuePosition(!0), a.options.hide && a.queueRemove(), a.state = "open", a.runModules("afterOpen")
            }), this
        },
        remove: function (b) {
            this.state = "closing", this.timerHide = !!b, this.runModules("beforeClose");
            var c = this;
            return this.timer && (window.clearTimeout(this.timer), this.timer = null), this.animateOut(function () {
                if (c.state = "closed", c.runModules("afterClose"), c.queuePosition(!0), c.options.remove && c.elem.detach(), c.runModules("beforeDestroy"), c.options.destroy && null !== PNotify.notices) {
                    var b = a.inArray(c, PNotify.notices);
                    -1 !== b && PNotify.notices.splice(b, 1)
                }
                c.runModules("afterDestroy")
            }), this
        },
        get: function () {
            return this.elem
        },
        parseOptions: function (b, c) {
            this.options = a.extend(!0, {}, PNotify.prototype.options), this.options.stack = PNotify.prototype.options.stack;
            var d, e, f = [b, c];
            for (e in f) {
                if (d = f[e], "undefined" == typeof d) break;
                if ("object" != typeof d) this.options.text = d; else for (var g in d) this.modules[g] ? a.extend(!0, this.options[g], d[g]) : this.options[g] = d[g]
            }
        },
        animateIn: function (a) {
            this.animating = "in";
            var b;
            b = "undefined" != typeof this.options.animation.effect_in ? this.options.animation.effect_in : this.options.animation, "none" === b ? (this.elem.show(), a()) : "show" === b ? this.elem.show(this.options.animate_speed, a) : "fade" === b ? this.elem.show().fadeTo(this.options.animate_speed, this.options.opacity, a) : "slide" === b ? this.elem.slideDown(this.options.animate_speed, a) : "function" == typeof b ? b("in", a, this.elem) : this.elem.show(b, "object" == typeof this.options.animation.options_in ? this.options.animation.options_in : {}, this.options.animate_speed, a), this.elem.parent().hasClass("ui-effects-wrapper") && this.elem.parent().css({
                position: "fixed",
                overflow: "visible"
            }), "slide" !== b && this.elem.css("overflow", "visible"), this.container.css("overflow", "hidden")
        },
        animateOut: function (a) {
            this.animating = "out";
            var b;
            b = "undefined" != typeof this.options.animation.effect_out ? this.options.animation.effect_out : this.options.animation, "none" === b ? (this.elem.hide(), a()) : "show" === b ? this.elem.hide(this.options.animate_speed, a) : "fade" === b ? this.elem.fadeOut(this.options.animate_speed, a) : "slide" === b ? this.elem.slideUp(this.options.animate_speed, a) : "function" == typeof b ? b("out", a, this.elem) : this.elem.hide(b, "object" == typeof this.options.animation.options_out ? this.options.animation.options_out : {}, this.options.animate_speed, a), this.elem.parent().hasClass("ui-effects-wrapper") && this.elem.parent().css({
                position: "fixed",
                overflow: "visible"
            }), "slide" !== b && this.elem.css("overflow", "visible"), this.container.css("overflow", "hidden")
        },
        position: function (a) {
            var b = this.options.stack, d = this.elem;
            if (d.parent().hasClass("ui-effects-wrapper") && (d = this.elem.css({
                left: "0",
                top: "0",
                right: "0",
                bottom: "0"
            }).parent()), "undefined" == typeof b.context && (b.context = c), b) {
                "number" != typeof b.nextpos1 && (b.nextpos1 = b.firstpos1), "number" != typeof b.nextpos2 && (b.nextpos2 = b.firstpos2), "number" != typeof b.addpos2 && (b.addpos2 = 0);
                var f = "none" === d.css("display");
                if (!f || a) {
                    var g, h, i = {};
                    switch (b.dir1) {
                        case"down":
                            h = "top";
                            break;
                        case"up":
                            h = "bottom";
                            break;
                        case"left":
                            h = "right";
                            break;
                        case"right":
                            h = "left"
                    }
                    a = parseInt(d.css(h).replace(/(?:\..*|[^0-9.])/g, "")), isNaN(a) && (a = 0), "undefined" != typeof b.firstpos1 || f || (b.firstpos1 = a, b.nextpos1 = b.firstpos1);
                    var j;
                    switch (b.dir2) {
                        case"down":
                            j = "top";
                            break;
                        case"up":
                            j = "bottom";
                            break;
                        case"left":
                            j = "right";
                            break;
                        case"right":
                            j = "left"
                    }
                    if (g = parseInt(d.css(j).replace(/(?:\..*|[^0-9.])/g, "")), isNaN(g) && (g = 0), "undefined" != typeof b.firstpos2 || f || (b.firstpos2 = g, b.nextpos2 = b.firstpos2), ("down" === b.dir1 && b.nextpos1 + d.height() > (b.context.is(c) ? e.height() : b.context.prop("scrollHeight")) || "up" === b.dir1 && b.nextpos1 + d.height() > (b.context.is(c) ? e.height() : b.context.prop("scrollHeight")) || "left" === b.dir1 && b.nextpos1 + d.width() > (b.context.is(c) ? e.width() : b.context.prop("scrollWidth")) || "right" === b.dir1 && b.nextpos1 + d.width() > (b.context.is(c) ? e.width() : b.context.prop("scrollWidth"))) && (b.nextpos1 = b.firstpos1, b.nextpos2 += b.addpos2 + ("undefined" == typeof b.spacing2 ? 25 : b.spacing2), b.addpos2 = 0), b.animation && b.nextpos2 < g) switch (b.dir2) {
                        case"down":
                            i.top = b.nextpos2 + "px";
                            break;
                        case"up":
                            i.bottom = b.nextpos2 + "px";
                            break;
                        case"left":
                            i.right = b.nextpos2 + "px";
                            break;
                        case"right":
                            i.left = b.nextpos2 + "px"
                    } else "number" == typeof b.nextpos2 && d.css(j, b.nextpos2 + "px");
                    switch (b.dir2) {
                        case"down":
                        case"up":
                            d.outerHeight(!0) > b.addpos2 && (b.addpos2 = d.height());
                            break;
                        case"left":
                        case"right":
                            d.outerWidth(!0) > b.addpos2 && (b.addpos2 = d.width())
                    }
                    if ("number" == typeof b.nextpos1) if (b.animation && (a > b.nextpos1 || i.top || i.bottom || i.right || i.left)) switch (b.dir1) {
                        case"down":
                            i.top = b.nextpos1 + "px";
                            break;
                        case"up":
                            i.bottom = b.nextpos1 + "px";
                            break;
                        case"left":
                            i.right = b.nextpos1 + "px";
                            break;
                        case"right":
                            i.left = b.nextpos1 + "px"
                    } else d.css(h, b.nextpos1 + "px");
                    switch ((i.top || i.bottom || i.right || i.left) && d.animate(i, {
                        duration: this.options.position_animate_speed,
                        queue: !1
                    }), b.dir1) {
                        case"down":
                        case"up":
                            b.nextpos1 += d.height() + ("undefined" == typeof b.spacing1 ? 25 : b.spacing1);
                            break;
                        case"left":
                        case"right":
                            b.nextpos1 += d.width() + ("undefined" == typeof b.spacing1 ? 25 : b.spacing1)
                    }
                }
                return this
            }
        },
        queuePosition: function (a, c) {
            return b && clearTimeout(b), c || (c = 10), b = setTimeout(function () {
                PNotify.positionAll(a)
            }, c), this
        },
        cancelRemove: function () {
            return this.timer && window.clearTimeout(this.timer), "closing" === this.state && (this.elem.stop(!0), this.state = "open", this.animating = "in", this.elem.css("height", "auto").animate({
                width: this.options.width,
                opacity: this.options.opacity
            }, "fast")), this
        },
        queueRemove: function () {
            var a = this;
            return this.cancelRemove(), this.timer = window.setTimeout(function () {
                a.remove(!0)
            }, isNaN(this.options.delay) ? 0 : this.options.delay), this
        }
    }), a.extend(PNotify, {
        notices: [],
        removeAll: function () {
            a.each(PNotify.notices, function () {
                this.remove && this.remove()
            })
        },
        positionAll: function (c) {
            b && clearTimeout(b), b = null, a.each(PNotify.notices, function () {
                var a = this.options.stack;
                a && (a.nextpos1 = a.firstpos1, a.nextpos2 = a.firstpos2, a.addpos2 = 0, a.animation = c)
            }), a.each(PNotify.notices, function () {
                this.position()
            })
        },
        styling: {
            jqueryui: {
                container: "ui-widget ui-widget-content ui-corner-all",
                notice: "ui-state-highlight",
                notice_icon: "ui-icon ui-icon-info",
                info: "",
                info_icon: "ui-icon ui-icon-info",
                success: "ui-state-default",
                success_icon: "ui-icon ui-icon-circle-check",
                error: "ui-state-error",
                error_icon: "ui-icon ui-icon-alert"
            },
            bootstrap2: {
                container: "alert",
                notice: "",
                notice_icon: "icon-exclamation-sign",
                info: "alert-info",
                info_icon: "icon-info-sign",
                success: "alert-success",
                success_icon: "icon-ok-sign",
                error: "alert-error",
                error_icon: "icon-warning-sign"
            },
            bootstrap3: {
                container: "alert",
                notice: "alert-warning",
                notice_icon: "glyphicon glyphicon-exclamation-sign",
                info: "alert-info",
                info_icon: "glyphicon glyphicon-info-sign",
                success: "alert-success",
                success_icon: "glyphicon glyphicon-ok-sign",
                error: "alert-danger",
                error_icon: "glyphicon glyphicon-warning-sign"
            }
        }
    }), PNotify.styling.fontawesome = a.extend({}, PNotify.styling.bootstrap3), a.extend(PNotify.styling.fontawesome, {
        notice_icon: "fa fa-exclamation-circle",
        info_icon: "fa fa-info",
        success_icon: "fa fa-check",
        error_icon: "fa fa-warning"
    }), document.body ? f() : a(f), PNotify
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.buttons", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    b.prototype.options.buttons = {
        closer: !0,
        closer_hover: !0,
        sticker: !0,
        sticker_hover: !0,
        labels: {close: "Close", stick: "Stick"}
    }, b.prototype.modules.buttons = {
        myOptions: null, closer: null, sticker: null, init: function (b, c) {
            var d = this;
            this.myOptions = c, b.elem.on({
                mouseenter: function (a) {
                    !d.myOptions.sticker || b.options.nonblock && b.options.nonblock.nonblock || d.sticker.trigger("pnotify_icon").css("visibility", "visible"), !d.myOptions.closer || b.options.nonblock && b.options.nonblock.nonblock || d.closer.css("visibility", "visible")
                }, mouseleave: function (a) {
                    d.myOptions.sticker_hover && d.sticker.css("visibility", "hidden"), d.myOptions.closer_hover && d.closer.css("visibility", "hidden")
                }
            }), this.sticker = a("<div />", {
                "class": "ui-pnotify-sticker",
                css: {cursor: "pointer", visibility: c.sticker_hover ? "hidden" : "visible"},
                click: function () {
                    b.options.hide = !b.options.hide, b.options.hide ? b.queueRemove() : b.cancelRemove(), a(this).trigger("pnotify_icon")
                }
            }).bind("pnotify_icon", function () {
                a(this).children().removeClass(b.styles.pin_up + " " + b.styles.pin_down).addClass(b.options.hide ? b.styles.pin_up : b.styles.pin_down)
            }).append(a("<span />", {
                "class": b.styles.pin_up,
                title: c.labels.stick
            })).prependTo(b.container), (!c.sticker || b.options.nonblock && b.options.nonblock.nonblock) && this.sticker.css("display", "none"), this.closer = a("<div />", {
                "class": "ui-pnotify-closer",
                css: {cursor: "pointer", visibility: c.closer_hover ? "hidden" : "visible"},
                click: function () {
                    b.remove(!1), d.sticker.css("visibility", "hidden"), d.closer.css("visibility", "hidden")
                }
            }).append(a("<span />", {
                "class": b.styles.closer,
                title: c.labels.close
            })).prependTo(b.container), (!c.closer || b.options.nonblock && b.options.nonblock.nonblock) && this.closer.css("display", "none")
        }, update: function (a, b) {
            this.myOptions = b, !b.closer || a.options.nonblock && a.options.nonblock.nonblock ? this.closer.css("display", "none") : b.closer && this.closer.css("display", "block"), !b.sticker || a.options.nonblock && a.options.nonblock.nonblock ? this.sticker.css("display", "none") : b.sticker && this.sticker.css("display", "block"), this.sticker.trigger("pnotify_icon"), b.sticker_hover ? this.sticker.css("visibility", "hidden") : a.options.nonblock && a.options.nonblock.nonblock || this.sticker.css("visibility", "visible"), b.closer_hover ? this.closer.css("visibility", "hidden") : a.options.nonblock && a.options.nonblock.nonblock || this.closer.css("visibility", "visible")
        }
    }, a.extend(b.styling.jqueryui, {
        closer: "ui-icon ui-icon-close",
        pin_up: "ui-icon ui-icon-pin-w",
        pin_down: "ui-icon ui-icon-pin-s"
    }), a.extend(b.styling.bootstrap2, {
        closer: "icon-remove",
        pin_up: "icon-pause",
        pin_down: "icon-play"
    }), a.extend(b.styling.bootstrap3, {
        closer: "glyphicon glyphicon-remove",
        pin_up: "glyphicon glyphicon-pause",
        pin_down: "glyphicon glyphicon-play"
    }), a.extend(b.styling.fontawesome, {closer: "fa fa-times", pin_up: "fa fa-pause", pin_down: "fa fa-play"})
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.callbacks", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    var c = b.prototype.init, d = b.prototype.open, e = b.prototype.remove;
    b.prototype.init = function () {
        this.options.before_init && this.options.before_init(this.options), c.apply(this, arguments), this.options.after_init && this.options.after_init(this)
    }, b.prototype.open = function () {
        var a;
        this.options.before_open && (a = this.options.before_open(this)), !1 !== a && (d.apply(this, arguments), this.options.after_open && this.options.after_open(this))
    }, b.prototype.remove = function (a) {
        var b;
        this.options.before_close && (b = this.options.before_close(this, a)), !1 !== b && (e.apply(this, arguments), this.options.after_close && this.options.after_close(this, a))
    }
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.confirm", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    b.prototype.options.confirm = {
        confirm: !1,
        prompt: !1,
        prompt_class: "",
        prompt_default: "",
        prompt_multi_line: !1,
        align: "right",
        buttons: [{
            text: "Ok", addClass: "", promptTrigger: !0, click: function (a, b) {
                a.remove(), a.get().trigger("pnotify.confirm", [a, b])
            }
        }, {
            text: "Cancel", addClass: "", click: function (a) {
                a.remove(), a.get().trigger("pnotify.cancel", a)
            }
        }]
    }, b.prototype.modules.confirm = {
        container: null, prompt: null, init: function (b, c) {
            this.container = a('<div style="margin-top:5px;clear:both;" />').css("text-align", c.align).appendTo(b.container), c.confirm || c.prompt ? this.makeDialog(b, c) : this.container.hide()
        }, update: function (a, b) {
            b.confirm ? (this.makeDialog(a, b), this.container.show()) : this.container.hide().empty()
        }, afterOpen: function (a, b) {
            b.prompt && this.prompt.focus()
        }, makeDialog: function (b, c) {
            var d, e, f = !1, g = this;
            this.container.empty(), c.prompt && (this.prompt = a("<" + (c.prompt_multi_line ? 'textarea rows="5"' : 'input type="text"') + ' style="margin-bottom:5px;clear:both;" />').addClass(b.styles.input + " " + c.prompt_class).val(c.prompt_default).appendTo(this.container));
            for (var h in c.buttons) d = c.buttons[h], f ? this.container.append(" ") : f = !0, e = a('<button type="button" />').addClass(b.styles.btn + " " + d.addClass).text(d.text).appendTo(this.container).on("click", function (a) {
                return function () {
                    "function" == typeof a.click && a.click(b, c.prompt ? g.prompt.val() : null)
                }
            }(d)), c.prompt && !c.prompt_multi_line && d.promptTrigger && this.prompt.keypress(function (a) {
                return function (b) {
                    13 == b.keyCode && a.click()
                }
            }(e)), b.styles.text && e.wrapInner('<span class="' + b.styles.text + '"></span>'), b.styles.btnhover && e.hover(function (a) {
                return function () {
                    a.addClass(b.styles.btnhover)
                }
            }(e), function (a) {
                return function () {
                    a.removeClass(b.styles.btnhover)
                }
            }(e)), b.styles.btnactive && e.on("mousedown", function (a) {
                return function () {
                    a.addClass(b.styles.btnactive)
                }
            }(e)).on("mouseup", function (a) {
                return function () {
                    a.removeClass(b.styles.btnactive)
                }
            }(e)), b.styles.btnfocus && e.on("focus", function (a) {
                return function () {
                    a.addClass(b.styles.btnfocus)
                }
            }(e)).on("blur", function (a) {
                return function () {
                    a.removeClass(b.styles.btnfocus)
                }
            }(e))
        }
    }, a.extend(b.styling.jqueryui, {
        btn: "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only",
        btnhover: "ui-state-hover",
        btnactive: "ui-state-active",
        btnfocus: "ui-state-focus",
        input: "",
        text: "ui-button-text"
    }), a.extend(b.styling.bootstrap2, {btn: "btn", input: ""}), a.extend(b.styling.bootstrap3, {
        btn: "btn btn-default",
        input: "form-control"
    }), a.extend(b.styling.fontawesome, {btn: "btn btn-default", input: "form-control"})
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.desktop", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    var c, d = function (a, b) {
        return (d = "Notification" in window ? function (a, b) {
            return new Notification(a, b)
        } : "mozNotification" in navigator ? function (a, b) {
            return navigator.mozNotification.createNotification(a, b.body, b.icon).show()
        } : "webkitNotifications" in window ? function (a, b) {
            return window.webkitNotifications.createNotification(b.icon, a, b.body)
        } : function (a, b) {
            return null
        })(a, b)
    };
    b.prototype.options.desktop = {desktop: !1, icon: null, tag: null}, b.prototype.modules.desktop = {
        tag: null, icon: null, genNotice: function (a, b) {
            this.icon = null === b.icon ? "http://sciactive.com/pnotify/includes/desktop/" + a.options.type + ".png" : !1 === b.icon ? null : b.icon, (null === this.tag || null !== b.tag) && (this.tag = null === b.tag ? "PNotify-" + Math.round(1e6 * Math.random()) : b.tag), a.desktop = d(a.options.title, {
                icon: this.icon,
                body: a.options.text,
                tag: this.tag
            }), "close" in a.desktop || (a.desktop.close = function () {
                a.desktop.cancel()
            }), a.desktop.onclick = function () {
                a.elem.trigger("click")
            }, a.desktop.onclose = function () {
                "closing" !== a.state && "closed" !== a.state && a.remove()
            }
        }, init: function (a, d) {
            d.desktop && (c = b.desktop.checkPermission(), 0 == c && this.genNotice(a, d))
        }, update: function (a, b, d) {
            0 == c && b.desktop && this.genNotice(a, b)
        }, beforeOpen: function (a, b) {
            0 == c && b.desktop && a.elem.css({left: "-10000px", display: "none"})
        }, afterOpen: function (a, b) {
            0 == c && b.desktop && (a.elem.css({
                left: "-10000px",
                display: "none"
            }), "show" in a.desktop && a.desktop.show())
        }, beforeClose: function (a, b) {
            0 == c && b.desktop && a.elem.css({left: "-10000px", display: "none"})
        }, afterClose: function (a, b) {
            0 == c && b.desktop && (a.elem.css({left: "-10000px", display: "none"}), a.desktop.close())
        }
    }, b.desktop = {
        permission: function () {
            "undefined" != typeof Notification && "requestPermission" in Notification ? Notification.requestPermission() : "webkitNotifications" in window && window.webkitNotifications.requestPermission()
        }, checkPermission: function () {
            return "undefined" != typeof Notification && "permission" in Notification ? "granted" == Notification.permission ? 0 : 1 : "webkitNotifications" in window ? window.webkitNotifications.checkPermission() : 1
        }
    }, c = b.desktop.checkPermission()
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.history", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    var c, d;
    a(function () {
        a("body").on("pnotify.history-all", function () {
            a.each(b.notices, function () {
                this.modules.history.inHistory && (this.elem.is(":visible") ? this.options.hide && this.queueRemove() : this.open && this.open())
            })
        }).on("pnotify.history-last", function () {
            var a, c = "top" === b.prototype.options.stack.push, d = c ? 0 : -1;
            do {
                if (a = -1 === d ? b.notices.slice(d) : b.notices.slice(d, d + 1), !a[0]) return !1;
                d = c ? d + 1 : d - 1
            } while (!a[0].modules.history.inHistory || a[0].elem.is(":visible"));
            a[0].open && a[0].open()
        })
    }), b.prototype.options.history = {
        history: !0,
        menu: !1,
        fixed: !0,
        maxonscreen: 1 / 0,
        labels: {redisplay: "Redisplay", all: "All", last: "Last"}
    }, b.prototype.modules.history = {
        inHistory: !1, init: function (b, e) {
            if (b.options.destroy = !1, this.inHistory = e.history, e.menu && "undefined" == typeof c) {
                c = a("<div />", {
                    "class": "ui-pnotify-history-container " + b.styles.hi_menu, mouseleave: function () {
                        c.animate({top: "-" + d + "px"}, {duration: 100, queue: !1})
                    }
                }).append(a("<div />", {
                    "class": "ui-pnotify-history-header",
                    text: e.labels.redisplay
                })).append(a("<button />", {
                    "class": "ui-pnotify-history-all " + b.styles.hi_btn,
                    text: e.labels.all,
                    mouseenter: function () {
                        a(this).addClass(b.styles.hi_btnhov)
                    },
                    mouseleave: function () {
                        a(this).removeClass(b.styles.hi_btnhov)
                    },
                    click: function () {
                        return a(this).trigger("pnotify.history-all"), !1
                    }
                })).append(a("<button />", {
                    "class": "ui-pnotify-history-last " + b.styles.hi_btn,
                    text: e.labels.last,
                    mouseenter: function () {
                        a(this).addClass(b.styles.hi_btnhov)
                    },
                    mouseleave: function () {
                        a(this).removeClass(b.styles.hi_btnhov)
                    },
                    click: function () {
                        return a(this).trigger("pnotify.history-last"), !1
                    }
                })).appendTo("body");
                var f = a("<span />", {
                    "class": "ui-pnotify-history-pulldown " + b.styles.hi_hnd,
                    mouseenter: function () {
                        c.animate({top: "0"}, {duration: 100, queue: !1})
                    }
                }).appendTo(c);
                console.log(f.offset()), d = f.offset().top + 2, c.css({top: "-" + d + "px"}), e.fixed && c.addClass("ui-pnotify-history-fixed")
            }
        }, update: function (a, b) {
            this.inHistory = b.history, b.fixed && c ? c.addClass("ui-pnotify-history-fixed") : c && c.removeClass("ui-pnotify-history-fixed")
        }, beforeOpen: function (c, d) {
            if (b.notices && b.notices.length > d.maxonscreen) {
                var e;
                e = "top" !== c.options.stack.push ? b.notices.slice(0, b.notices.length - d.maxonscreen) : b.notices.slice(d.maxonscreen, b.notices.length), a.each(e, function () {
                    this.remove && this.remove()
                })
            }
        }
    }, a.extend(b.styling.jqueryui, {
        hi_menu: "ui-state-default ui-corner-bottom",
        hi_btn: "ui-state-default ui-corner-all",
        hi_btnhov: "ui-state-hover",
        hi_hnd: "ui-icon ui-icon-grip-dotted-horizontal"
    }), a.extend(b.styling.bootstrap2, {
        hi_menu: "well",
        hi_btn: "btn",
        hi_btnhov: "",
        hi_hnd: "icon-chevron-down"
    }), a.extend(b.styling.bootstrap3, {
        hi_menu: "well",
        hi_btn: "btn btn-default",
        hi_btnhov: "",
        hi_hnd: "glyphicon glyphicon-chevron-down"
    }), a.extend(b.styling.fontawesome, {
        hi_menu: "well",
        hi_btn: "btn btn-default",
        hi_btnhov: "",
        hi_hnd: "fa fa-chevron-down"
    })
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.nonblock", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    var c, d = /^on/, e = /^(dbl)?click$|^mouse(move|down|up|over|out|enter|leave)$|^contextmenu$/,
        f = /^(focus|blur|select|change|reset)$|^key(press|down|up)$/, g = /^(scroll|resize|(un)?load|abort|error)$/,
        h = function (b, c) {
            var h;
            b = b.toLowerCase(), document.createEvent && this.dispatchEvent ? (b = b.replace(d, ""), b.match(e) ? (a(this).offset(), h = document.createEvent("MouseEvents"), h.initMouseEvent(b, c.bubbles, c.cancelable, c.view, c.detail, c.screenX, c.screenY, c.clientX, c.clientY, c.ctrlKey, c.altKey, c.shiftKey, c.metaKey, c.button, c.relatedTarget)) : b.match(f) ? (h = document.createEvent("UIEvents"), h.initUIEvent(b, c.bubbles, c.cancelable, c.view, c.detail)) : b.match(g) && (h = document.createEvent("HTMLEvents"), h.initEvent(b, c.bubbles, c.cancelable)), h && this.dispatchEvent(h)) : (b.match(d) || (b = "on" + b), h = document.createEventObject(c), this.fireEvent(b, h))
        }, i = function (b, d, e) {
            b.elem.css("display", "none");
            var f = document.elementFromPoint(d.clientX, d.clientY);
            b.elem.css("display", "block");
            var g = a(f), i = g.css("cursor");
            b.elem.css("cursor", "auto" !== i ? i : "default"), c && c.get(0) == f || (c && (h.call(c.get(0), "mouseleave", d.originalEvent), h.call(c.get(0), "mouseout", d.originalEvent)), h.call(f, "mouseenter", d.originalEvent), h.call(f, "mouseover", d.originalEvent)), h.call(f, e, d.originalEvent), c = g
        };
    b.prototype.options.nonblock = {nonblock: !1, nonblock_opacity: .2}, b.prototype.modules.nonblock = {
        myOptions: null, init: function (a, b) {
            var d = this;
            this.myOptions = b, a.elem.on({
                mouseenter: function (b) {
                    d.myOptions.nonblock && b.stopPropagation(), d.myOptions.nonblock && a.elem.stop().animate({opacity: d.myOptions.nonblock_opacity}, "fast")
                }, mouseleave: function (b) {
                    d.myOptions.nonblock && b.stopPropagation(), c = null, a.elem.css("cursor", "auto"), d.myOptions.nonblock && "out" !== a.animating && a.elem.stop().animate({opacity: a.options.opacity}, "fast")
                }, mouseover: function (a) {
                    d.myOptions.nonblock && a.stopPropagation()
                }, mouseout: function (a) {
                    d.myOptions.nonblock && a.stopPropagation()
                }, mousemove: function (b) {
                    d.myOptions.nonblock && (b.stopPropagation(), i(a, b, "onmousemove"))
                }, mousedown: function (b) {
                    d.myOptions.nonblock && (b.stopPropagation(), b.preventDefault(), i(a, b, "onmousedown"))
                }, mouseup: function (b) {
                    d.myOptions.nonblock && (b.stopPropagation(), b.preventDefault(), i(a, b, "onmouseup"))
                }, click: function (b) {
                    d.myOptions.nonblock && (b.stopPropagation(), i(a, b, "onclick"))
                }, dblclick: function (b) {
                    d.myOptions.nonblock && (b.stopPropagation(), i(a, b, "ondblclick"))
                }
            })
        }, update: function (a, b) {
            this.myOptions = b
        }
    }
}), function (a) {
    "function" == typeof define && define.amd ? define("pnotify.reference", ["jquery", "pnotify"], a) : a(jQuery, PNotify)
}(function (a, b) {
    b.prototype.options.reference = {putThing: !1, labels: {text: "Spin Around"}}, b.prototype.modules.reference = {
        thingElem: null, init: function (b, c) {
            var d = this;
            c.putThing && (this.thingElem = a('<button style="float:right;" class="btn btn-default" type="button" disabled><i class="' + b.styles.athing + '" />&nbsp;' + c.labels.text + "</button>").appendTo(b.container), b.container.append('<div style="clear: right; line-height: 0;" />'), b.elem.on({
                mouseenter: function (a) {
                    d.thingElem.prop("disabled", !1)
                }, mouseleave: function (a) {
                    d.thingElem.prop("disabled", !0)
                }
            }), this.thingElem.on("click", function () {
                var a = 0, c = setInterval(function () {
                    a += 10, 360 == a && (a = 0, clearInterval(c)), b.elem.css({
                        "-moz-transform": "rotate(" + a + "deg)",
                        "-webkit-transform": "rotate(" + a + "deg)",
                        "-o-transform": "rotate(" + a + "deg)",
                        "-ms-transform": "rotate(" + a + "deg)",
                        filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=" + a / 360 * 4 + ")"
                    })
                }, 20)
            }))
        }, update: function (a, b, c) {
            b.putThing && this.thingElem ? this.thingElem.show() : !b.putThing && this.thingElem && this.thingElem.hide(), this.thingElem && this.thingElem.find("i").attr("class", a.styles.athing)
        }, beforeOpen: function (a, b) {
        }, afterOpen: function (a, b) {
        }, beforeClose: function (a, b) {
        }, afterClose: function (a, b) {
        }, beforeDestroy: function (a, b) {
        }, afterDestroy: function (a, b) {
        }
    }, a.extend(b.styling.jqueryui, {athing: "ui-icon ui-icon-refresh"}), a.extend(b.styling.bootstrap2, {athing: "icon-refresh"}), a.extend(b.styling.bootstrap3, {athing: "glyphicon glyphicon-refresh"}), a.extend(b.styling.fontawesome, {athing: "fa fa-refresh"})
}), function (a) {
    a.isScrollToFixed = function (b) {
        return !!a(b).data("ScrollToFixed")
    }, a.ScrollToFixed = function (b, c) {
        function d() {
            x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed"), B = -1, y = x.offset().top, z = x.offset().left, q.options.offsets && (z += x.offset().left - x.position().left), -1 == A && (A = z), r = x.css("position"), w = !0, -1 != q.options.bottom && (x.trigger("preFixed.ScrollToFixed"), i(), x.trigger("fixed.ScrollToFixed"))
        }

        function e() {
            var a = q.options.limit;
            return a ? "function" == typeof a ? a.apply(x) : a : 0
        }

        function f() {
            return "fixed" === r
        }

        function g() {
            return "absolute" === r
        }

        function h() {
            return !(f() || g())
        }

        function i() {
            if (!f()) {
                var a = x[0].getBoundingClientRect();
                C.css({
                    display: x.css("display"),
                    width: a.width,
                    height: a.height,
                    "float": x.css("float")
                }), cssOptions = {
                    "z-index": q.options.zIndex,
                    position: "fixed",
                    top: -1 == q.options.bottom ? m() : "",
                    bottom: -1 == q.options.bottom ? "" : q.options.bottom,
                    "margin-left": "0px"
                }, q.options.dontSetWidth || (cssOptions.width = x.css("width")), x.css(cssOptions), x.addClass(q.options.baseClassName), q.options.className && x.addClass(q.options.className), r = "fixed"
            }
        }

        function j() {
            var a = e(), b = z;
            q.options.removeOffsets && (b = "", a -= y), cssOptions = {
                position: "absolute",
                top: a,
                left: b,
                "margin-left": "0px",
                bottom: ""
            }, q.options.dontSetWidth || (cssOptions.width = x.css("width")), x.css(cssOptions), r = "absolute"
        }

        function k() {
            h() || (B = -1, C.css("display", "none"), x.css({
                "z-index": v,
                width: "",
                position: s,
                left: "",
                top: u,
                "margin-left": ""
            }), x.removeClass("scroll-to-fixed-fixed"), q.options.className && x.removeClass(q.options.className), r = null)
        }

        function l(a) {
            a != B && (x.css("left", z - a), B = a)
        }

        function m() {
            var a = q.options.marginTop;
            return a ? "function" == typeof a ? a.apply(x) : a : 0
        }

        function n() {
            if (a.isScrollToFixed(x) && !x.is(":hidden")) {
                var b = w, c = h();
                w ? h() && (y = x.offset().top, z = x.offset().left) : d();
                var n = a(window).scrollLeft(), r = a(window).scrollTop(), t = e();
                q.options.minWidth && a(window).width() < q.options.minWidth ? h() && b || (p(), x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed")) : q.options.maxWidth && a(window).width() > q.options.maxWidth ? h() && b || (p(), x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed")) : -1 == q.options.bottom ? t > 0 && r >= t - m() ? c || g() && b || (p(), x.trigger("preAbsolute.ScrollToFixed"), j(), x.trigger("unfixed.ScrollToFixed")) : r >= y - m() ? (f() && b || (p(), x.trigger("preFixed.ScrollToFixed"), i(), B = -1, x.trigger("fixed.ScrollToFixed")), l(n)) : h() && b || (p(), x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed")) : t > 0 ? r + a(window).height() - x.outerHeight(!0) >= t - (m() || -o()) ? f() && (p(), x.trigger("preUnfixed.ScrollToFixed"), "absolute" === s ? j() : k(), x.trigger("unfixed.ScrollToFixed")) : (f() || (p(), x.trigger("preFixed.ScrollToFixed"), i()), l(n), x.trigger("fixed.ScrollToFixed")) : l(n)
            }
        }

        function o() {
            return q.options.bottom ? q.options.bottom : 0
        }

        function p() {
            var a = x.css("position");
            "absolute" == a ? x.trigger("postAbsolute.ScrollToFixed") : "fixed" == a ? x.trigger("postFixed.ScrollToFixed") : x.trigger("postUnfixed.ScrollToFixed")
        }

        var q = this;
        q.$el = a(b), q.el = b, q.$el.data("ScrollToFixed", q);
        var r, s, t, u, v, w = !1, x = q.$el, y = 0, z = 0, A = -1, B = -1, C = null, D = function (a) {
            x.is(":visible") && (w = !1, n())
        }, E = function (a) {
            window.requestAnimationFrame ? requestAnimationFrame(n) : n()
        }, F = function (a) {
            a = a || window.event, a.preventDefault && a.preventDefault(), a.returnValue = !1
        };
        q.init = function () {
            q.options = a.extend({}, a.ScrollToFixed.defaultOptions, c), v = x.css("z-index"), q.$el.css("z-index", q.options.zIndex), C = a("<div />"), r = x.css("position"), s = x.css("position"), t = x.css("float"), u = x.css("top"), h() && q.$el.after(C), a(window).bind("resize.ScrollToFixed", D), a(window).bind("scroll.ScrollToFixed", E), "ontouchmove" in window && a(window).bind("touchmove.ScrollToFixed", n),
            q.options.preFixed && x.bind("preFixed.ScrollToFixed", q.options.preFixed), q.options.postFixed && x.bind("postFixed.ScrollToFixed", q.options.postFixed), q.options.preUnfixed && x.bind("preUnfixed.ScrollToFixed", q.options.preUnfixed), q.options.postUnfixed && x.bind("postUnfixed.ScrollToFixed", q.options.postUnfixed), q.options.preAbsolute && x.bind("preAbsolute.ScrollToFixed", q.options.preAbsolute), q.options.postAbsolute && x.bind("postAbsolute.ScrollToFixed", q.options.postAbsolute), q.options.fixed && x.bind("fixed.ScrollToFixed", q.options.fixed), q.options.unfixed && x.bind("unfixed.ScrollToFixed", q.options.unfixed), q.options.spacerClass && C.addClass(q.options.spacerClass), x.bind("resize.ScrollToFixed", function () {
                C.height(x.height())
            }), x.bind("scroll.ScrollToFixed", function () {
                x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed"), n()
            }), x.bind("detach.ScrollToFixed", function (b) {
                F(b), x.trigger("preUnfixed.ScrollToFixed"), k(), x.trigger("unfixed.ScrollToFixed"), a(window).unbind("resize.ScrollToFixed", D), a(window).unbind("scroll.ScrollToFixed", E), x.unbind(".ScrollToFixed"), C.remove(), q.$el.removeData("ScrollToFixed")
            }), D()
        }, q.init()
    }, a.ScrollToFixed.defaultOptions = {
        marginTop: 0,
        limit: 0,
        bottom: -1,
        zIndex: 1e3,
        baseClassName: "scroll-to-fixed-fixed"
    }, a.fn.scrollToFixed = function (b) {
        return this.each(function () {
            new a.ScrollToFixed(this, b)
        })
    }
}(jQuery), !function (a) {
    "function" == typeof define && define.amd ? define(["jquery"], a) : a("object" == typeof exports ? require("jquery") : jQuery)
}(function (a) {
    var b, c = navigator.userAgent, d = /iphone/i.test(c), e = /chrome/i.test(c), f = /android/i.test(c);
    a.mask = {
        definitions: {9: "[0-9]", a: "[A-Za-z]", "*": "[A-Za-z0-9]"},
        autoclear: !0,
        dataName: "rawMaskFn",
        placeholder: "_"
    }, a.fn.extend({
        caret: function (a, b) {
            var c;
            return 0 === this.length || this.is(":hidden") ? void 0 : "number" == typeof a ? (b = "number" == typeof b ? b : a, this.each(function () {
                this.setSelectionRange ? this.setSelectionRange(a, b) : this.createTextRange && (c = this.createTextRange(), c.collapse(!0), c.moveEnd("character", b), c.moveStart("character", a), c.select())
            })) : (this[0].setSelectionRange ? (a = this[0].selectionStart, b = this[0].selectionEnd) : document.selection && document.selection.createRange && (c = document.selection.createRange(), a = 0 - c.duplicate().moveStart("character", -1e5), b = a + c.text.length), {
                begin: a,
                end: b
            })
        }, unmask: function () {
            return this.trigger("unmask")
        }, mask: function (c, g) {
            var h, i, j, k, l, m, n, o;
            if (!c && this.length > 0) {
                h = a(this[0]);
                var p = h.data(a.mask.dataName);
                return p ? p() : void 0
            }
            return g = a.extend({
                autoclear: a.mask.autoclear,
                placeholder: a.mask.placeholder,
                completed: null
            }, g), i = a.mask.definitions, j = [], k = n = c.length, l = null, a.each(c.split(""), function (a, b) {
                "?" == b ? (n--, k = a) : i[b] ? (j.push(new RegExp(i[b])), null === l && (l = j.length - 1), k > a && (m = j.length - 1)) : j.push(null)
            }), this.trigger("unmask").each(function () {
                function h() {
                    if (g.completed) {
                        for (var a = l; m >= a; a++) if (j[a] && C[a] === p(a)) return;
                        g.completed.call(B)
                    }
                }

                function p(a) {
                    return g.placeholder.charAt(a < g.placeholder.length ? a : 0)
                }

                function q(a) {
                    for (; ++a < n && !j[a];) ;
                    return a
                }

                function r(a) {
                    for (; --a >= 0 && !j[a];) ;
                    return a
                }

                function s(a, b) {
                    var c, d;
                    if (!(0 > a)) {
                        for (c = a, d = q(b); n > c; c++) if (j[c]) {
                            if (!(n > d && j[c].test(C[d]))) break;
                            C[c] = C[d], C[d] = p(d), d = q(d)
                        }
                        z(), B.caret(Math.max(l, a))
                    }
                }

                function t(a) {
                    var b, c, d, e;
                    for (b = a, c = p(a); n > b; b++) if (j[b]) {
                        if (d = q(b), e = C[b], C[b] = c, !(n > d && j[d].test(e))) break;
                        c = e
                    }
                }

                function u() {
                    var a = B.val(), b = B.caret();
                    if (o && o.length && o.length > a.length) {
                        for (A(!0); b.begin > 0 && !j[b.begin - 1];) b.begin--;
                        if (0 === b.begin) for (; b.begin < l && !j[b.begin];) b.begin++;
                        B.caret(b.begin, b.begin)
                    } else {
                        for (A(!0); b.begin < n && !j[b.begin];) b.begin++;
                        B.caret(b.begin, b.begin)
                    }
                    h()
                }

                function v() {
                    A(), B.val() != E && B.change()
                }

                function w(a) {
                    if (!B.prop("readonly")) {
                        var b, c, e, f = a.which || a.keyCode;
                        o = B.val(), 8 === f || 46 === f || d && 127 === f ? (b = B.caret(), c = b.begin, e = b.end, e - c === 0 && (c = 46 !== f ? r(c) : e = q(c - 1), e = 46 === f ? q(e) : e), y(c, e), s(c, e - 1), a.preventDefault()) : 13 === f ? v.call(this, a) : 27 === f && (B.val(E), B.caret(0, A()), a.preventDefault())
                    }
                }

                function x(b) {
                    if (!B.prop("readonly")) {
                        var c, d, e, g = b.which || b.keyCode, i = B.caret();
                        if (!(b.ctrlKey || b.altKey || b.metaKey || 32 > g) && g && 13 !== g) {
                            if (i.end - i.begin !== 0 && (y(i.begin, i.end), s(i.begin, i.end - 1)), c = q(i.begin - 1), n > c && (d = String.fromCharCode(g), j[c].test(d))) {
                                if (t(c), C[c] = d, z(), e = q(c), f) {
                                    var k = function () {
                                        a.proxy(a.fn.caret, B, e)()
                                    };
                                    setTimeout(k, 0)
                                } else B.caret(e);
                                i.begin <= m && h()
                            }
                            b.preventDefault()
                        }
                    }
                }

                function y(a, b) {
                    var c;
                    for (c = a; b > c && n > c; c++) j[c] && (C[c] = p(c))
                }

                function z() {
                    B.val(C.join(""))
                }

                function A(a) {
                    var b, c, d, e = B.val(), f = -1;
                    for (b = 0, d = 0; n > b; b++) if (j[b]) {
                        for (C[b] = p(b); d++ < e.length;) if (c = e.charAt(d - 1), j[b].test(c)) {
                            C[b] = c, f = b;
                            break
                        }
                        if (d > e.length) {
                            y(b + 1, n);
                            break
                        }
                    } else C[b] === e.charAt(d) && d++, k > b && (f = b);
                    return a ? z() : k > f + 1 ? g.autoclear || C.join("") === D ? (B.val() && B.val(""), y(0, n)) : z() : (z(), B.val(B.val().substring(0, f + 1))), k ? b : l
                }

                var B = a(this), C = a.map(c.split(""), function (a, b) {
                    return "?" != a ? i[a] ? p(b) : a : void 0
                }), D = C.join(""), E = B.val();
                B.data(a.mask.dataName, function () {
                    return a.map(C, function (a, b) {
                        return j[b] && a != p(b) ? a : null
                    }).join("")
                }), B.one("unmask", function () {
                    B.off(".mask").removeData(a.mask.dataName)
                }).on("focus.mask", function () {
                    if (!B.prop("readonly")) {
                        clearTimeout(b);
                        var a;
                        E = B.val(), a = A(), b = setTimeout(function () {
                            B.get(0) === document.activeElement && (z(), a == c.replace("?", "").length ? B.caret(0, a) : B.caret(a))
                        }, 10)
                    }
                }).on("blur.mask", v).on("keydown.mask", w).on("keypress.mask", x).on("input.mask paste.mask", function () {
                    B.prop("readonly") || setTimeout(function () {
                        var a = A(!0);
                        B.caret(a), h()
                    }, 0)
                }), e && f && B.off("input.mask").on("input.mask", u), A()
            })
        }
    })
}), function (a) {
    "use strict";
    a.fn.formApi = function (b) {
        return d[b] ? d[b].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof b && b ? (a.error("Method " + b + " does not exist on jQuery.formApi"), !1) : d.init.apply(this, arguments)
    };
    var b = {flagrequest: 0, timeout_id: void 0, keyUpValidateTime: 300, keyUpValidateField: null}, c = {
        id: void 0,
        formGroupTarget: ".form-group",
        helpCssClass: "help-block",
        errorCssClass: "has-error",
        successCssClass: "has-success"
    }, d = {
        init: function (b) {
            return window.FileReader && window.FormData ? this.each(function () {
                var f = a(this);
                if (!f.data("formApi")) {
                    var g = b.fields ? b.fields.slice() : [], h = b.validateFields ? b.validateFields.slice() : [],
                        i = b.scenarios ? b.scenarios : {}, j = b.scenario ? b.scenario : null,
                        k = b.validateFieldsKeyUp ? b.validateFieldsKeyUp.slice() : [], l = b.extraSubmitFields || [];
                    if (a.isArray(h) ? a.each(h, function (b) {
                        a.isPlainObject(h) ? h[b] = a.extend({}, c, this) : h[b] = a.extend({}, c, {id: h[b]})
                    }) : h = [], k) for (var m = k.length; m--;) document.getElementById(k[m]).onkeyup = d.validateFieldsKeyUp, document.getElementById(k[m]).onchange = d.validateFieldsKeyUp, document.getElementById(k[m]).oninput = d.validateFieldsKeyUp;
                    f.data("formApi", {
                        targetForm: f,
                        settings: b,
                        fields: g,
                        extraSubmitFields: l,
                        validateFields: h,
                        scenarios: i,
                        scenario: j,
                        files: [],
                        addFile: function (a) {
                            this.files.push(a)
                        },
                        removeFile: function (a) {
                            var b = this.files;
                            return b.forEach(function (c, d) {
                                return c[a] ? (b.splice(d, 1), !0) : void 0
                            }), !1
                        },
                        clearFiles: function () {
                            this.files = []
                        },
                        reset: function (a) {
                            a && "files" == a ? this.clearFiles() : (a || this.clearFiles(), e(this.targetForm, a))
                        }
                    }), f.on("submit.formApi", d.submitForm)
                }
            }) : void console.log("Ваш браузер устарел")
        }, validateFieldsKeyUp: function (c) {
            b.timeout_id && clearTimeout(b.timeout_id), b.keyUpValidateField = this.id, b.timeout_id = window.setTimeout(function () {
                b.timeout_id && clearTimeout(b.timeout_id);
                var e = new FormData, f = a(c.target).parents("form"), g = f.data("formApi");
                d.getFields(g, e), d.sendForm(g, e)
            }, b.keyUpValidateTime)
        }, submitForm: function (c) {
            var e = new FormData, f = a(this), g = f.data("formApi");
            if (c && (d.stopPropagation(c), d.deleteEvent(c), b.keyUpValidateField = null, g.settings.beforeSubmit && g.settings.beforeSubmit(g)), d.getFields(g, e), c && g.extraSubmitFields) for (var h in g.extraSubmitFields) e.append(h, g.extraSubmitFields[h]);
            d.sendForm(g, e)
        }, getFields: function (a, b) {
            for (var c = a.targetForm, e = a.fields, f = e.length; f--;) {
                var g = c.find('[name ^= "' + e[f] + '"]');
                if (1 == g.length) {
                    if ("radio" == g.attr("type") && !g.prop("checked")) return;
                    if ("checkbox" == g.attr("type") && !g.prop("checked")) return;
                    b.append([e[f]], g.val() || "")
                } else g.length > 1 && g.each(function () {
                    ("radio" != this.type || this.checked) && ("checkbox" != this.type || this.checked) && b.append([this.name], this.value || "")
                })
            }
            a.files.length && d.getFiles(c, b)
        }, sendForm: function (c, e) {
            var f = c.targetForm;
            b.flagrequest || (b.flagrequest = 1, f.find("[type='submit']").attr("disabled", "disabled"), a.ajax({
                url: f.attr("action"),
                type: f.attr("method"),
                data: e,
                processData: !1,
                contentType: !1,
                complete: function (a, d) {
                    b.flagrequest = 0, f.find("[type='submit']").removeAttr("disabled");
                    var e = a.getResponseHeader("X-Redirect");
                    e && (window.location = e), c.settings.complete && c.settings.complete(c, a, d)
                },
                beforeSend: function (a, b) {
                    c.settings.beforeSend && c.settings.beforeSend(c, a, b)
                },
                success: function (a) {
                    d.responseSuccess(c, a)
                },
                error: function (a) {
                    d.responseError(c, a)
                }
            }))
        }, responseError: function (a, b) {
            (!b.status || 301 != b.status && 302 != b.status && 500 != b.status) && a.settings.error && a.settings.error(a, b)
        }, responseSuccess: function (c, e) {
            if (!a.isPlainObject(e) && !a.isArray(e)) return void d.responseError(c, e);
            c.targetForm, b.fields;
            c.settings.beforeValidate && c.settings.beforeValidate(c), c.settings.validateCostum || g(c, e), c.settings.afterValidate && c.settings.afterValidate(c), c.settings.success && c.settings.success(c, e)
        }, getFiles: function (a, b) {
            for (var c = a.data("formApi"), d = 0, e = c.files.length; e > d; d++) for (var f in c.files[d]) b.append(f, c.files[d][f])
        }, stopPropagation: function (a) {
            a.stopPropagation ? a.stopPropagation() : a.cancelBubble = !0
        }, deleteEvent: function (a) {
            a.preventDefault ? a.preventDefault() : a.returnValue = !1
        }
    }, e = function (a, b) {
        var c = a.data("formApi"), d = [];
        for (var e in c.validateFields) b ? b == c.validateFields[e].id ? d.push(c.validateFields[e].id) : b.search("*" == c.validateFields[e].id.substr(-1)) && b.search(c.validateFields[e].id.substring(0, c.validateFields[e].id.length - 1)) >= 0 && d.push(b) : d.push(c.validateFields[e].id);
        for (var e in d) {
            var g = c.targetForm.find("#" + d[e]);
            g && (f(g), g.trigger("reset"), g.parents(c.validateFields[e].formGroupTarget).removeClass(c.validateFields[e].successCssClass).removeClass(c.validateFields[e].errorCssClass).find("." + c.validateFields[e].helpCssClass).html(""))
        }
        g = c.targetForm.find(b), g.length && g.each(function () {
            "text" == this.type || "textarea" == this.type ? this.value = "" : "radio" == this.type || "checkbox" == this.type ? this.checked = !1 : ("select-one" == this.type || "select-multiple" == this.type) && (this.value = "All")
        })
    }, f = function (a) {
        a.each(function () {
            "text" == this.type || "textarea" == this.type ? this.value = "" : "radio" == this.type || "checkbox" == this.type ? this.checked = !1 : ("select-one" == this.type || "select-multiple" == this.type) && (this.value = "All")
        })
    }, g = function (c, d) {
        var e = c.validateFields, f = [];
        c.scenario && c.scenarios[c.scenario] && (f = c.scenarios[c.scenario]);
        var g, h, i, j = {};
        for (var k in d) if (g = k.replace(/\d-/, ""), !(f.length > 0 && a.inArray(g, f) < 0)) {
            h = {};
            for (var l in e) {
                var m = e[l].id;
                if ("*" == m.substr(-1) && g.search(m.substring(0, g.length - 1)) >= 0 && (m = g), g == m) {
                    h = e[l];
                    break
                }
            }
            if (h) {
                if (i = c.targetForm.find("#" + g), j[g] = d[k], !i.val() && b.keyUpValidateField && b.keyUpValidateField != g) continue;
                i.parents(h.formGroupTarget).addClass(h.errorCssClass).removeClass(h.successCssClass).find("." + h.helpCssClass).html(d[k])
            }
        }
        for (var k in e) if (g = e[k].id, h = {}, !(f.length > 0 && a.inArray(g, f) < 0)) if ("*" != g.substr(-1)) {
            if (!j[g]) {
                if (h = e[k], i = c.targetForm.find("#" + g), !i.val() && b.keyUpValidateField && b.keyUpValidateField != g) continue;
                i.parents(h.formGroupTarget).addClass(h.successCssClass).removeClass(h.errorCssClass).find("." + h.helpCssClass).html("")
            }
        } else h = c.validateFields[k], g = g.substring(0, g.length - 1), i = c.targetForm.find('[id ^= "' + g + '"]'), i.each(function () {
            var b = a(this);
            j[b.attr("id")] || b.parents(h.formGroupTarget).addClass(h.successCssClass).removeClass(h.errorCssClass).find("." + h.helpCssClass).html("")
        })
    }
}(window.jQuery), function (a, b) {
    "use strict";
    "object" == typeof exports ? module.exports = b(require("./punycode"), require("./IPv6"), require("./SecondLevelDomains")) : "function" == typeof define && define.amd ? define(["./punycode", "./IPv6", "./SecondLevelDomains"], b) : a.URI = b(a.punycode, a.IPv6, a.SecondLevelDomains, a)
}(this, function (a, b, c, d) {
    "use strict";

    function e(a, b) {
        var c = arguments.length >= 1, d = arguments.length >= 2;
        if (!(this instanceof e)) return c ? d ? new e(a, b) : new e(a) : new e;
        if (void 0 === a) {
            if (c) throw new TypeError("undefined is not a valid argument for URI");
            a = "undefined" != typeof location ? location.href + "" : ""
        }
        return this.href(a), void 0 !== b ? this.absoluteTo(b) : this
    }

    function f(a) {
        return a.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1")
    }

    function g(a) {
        return void 0 === a ? "Undefined" : String(Object.prototype.toString.call(a)).slice(8, -1)
    }

    function h(a) {
        return "Array" === g(a)
    }

    function i(a, b) {
        var c, d, e = {};
        if ("RegExp" === g(b)) e = null; else if (h(b)) for (c = 0, d = b.length; d > c; c++) e[b[c]] = !0; else e[b] = !0;
        for (c = 0, d = a.length; d > c; c++) {
            var f = e && void 0 !== e[a[c]] || !e && b.test(a[c]);
            f && (a.splice(c, 1), d--, c--)
        }
        return a
    }

    function j(a, b) {
        var c, d;
        if (h(b)) {
            for (c = 0, d = b.length; d > c; c++) if (!j(a, b[c])) return !1;
            return !0
        }
        var e = g(b);
        for (c = 0, d = a.length; d > c; c++) if ("RegExp" === e) {
            if ("string" == typeof a[c] && a[c].match(b)) return !0
        } else if (a[c] === b) return !0;
        return !1
    }

    function k(a, b) {
        if (!h(a) || !h(b)) return !1;
        if (a.length !== b.length) return !1;
        a.sort(), b.sort();
        for (var c = 0, d = a.length; d > c; c++) if (a[c] !== b[c]) return !1;
        return !0
    }

    function l(a) {
        var b = /^\/+|\/+$/g;
        return a.replace(b, "")
    }

    function m(a) {
        return escape(a)
    }

    function n(a) {
        return encodeURIComponent(a).replace(/[!'()*]/g, m).replace(/\*/g, "%2A")
    }

    function o(a) {
        return function (b, c) {
            return void 0 === b ? this._parts[a] || "" : (this._parts[a] = b || null, this.build(!c), this)
        }
    }

    function p(a, b) {
        return function (c, d) {
            return void 0 === c ? this._parts[a] || "" : (null !== c && (c += "", c.charAt(0) === b && (c = c.substring(1))), this._parts[a] = c, this.build(!d), this)
        }
    }

    var q = d && d.URI;
    e.version = "1.17.0";
    var r = e.prototype, s = Object.prototype.hasOwnProperty;
    e._parts = function () {
        return {
            protocol: null,
            username: null,
            password: null,
            hostname: null,
            urn: null,
            port: null,
            path: null,
            query: null,
            fragment: null,
            duplicateQueryParameters: e.duplicateQueryParameters,
            escapeQuerySpace: e.escapeQuerySpace
        }
    }, e.duplicateQueryParameters = !1, e.escapeQuerySpace = !0, e.protocol_expression = /^[a-z][a-z0-9.+-]*$/i, e.idn_expression = /[^a-z0-9\.-]/i, e.punycode_expression = /(xn--)/i, e.ip4_expression = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/, e.ip6_expression = /^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/, e.find_uri_expression = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/gi, e.findUri = {
        start: /\b(?:([a-z][a-z0-9.+-]*:\/\/)|www\.)/gi,
        end: /[\s\r\n]|$/,
        trim: /[`!()\[\]{};:'".,<>?«»“”„‘’]+$/
    }, e.defaultPorts = {
        http: "80",
        https: "443",
        ftp: "21",
        gopher: "70",
        ws: "80",
        wss: "443"
    }, e.invalid_hostname_characters = /[^a-zA-Z0-9\.-]/, e.domAttributes = {
        a: "href",
        blockquote: "cite",
        link: "href",
        base: "href",
        script: "src",
        form: "action",
        img: "src",
        area: "href",
        iframe: "src",
        embed: "src",
        source: "src",
        track: "src",
        input: "src",
        audio: "src",
        video: "src"
    }, e.getDomAttribute = function (a) {
        if (a && a.nodeName) {
            var b = a.nodeName.toLowerCase();
            if ("input" !== b || "image" === a.type) return e.domAttributes[b]
        }
    }, e.encode = n, e.decode = decodeURIComponent, e.iso8859 = function () {
        e.encode = escape, e.decode = unescape
    }, e.unicode = function () {
        e.encode = n, e.decode = decodeURIComponent
    }, e.characters = {
        pathname: {
            encode: {
                expression: /%(24|26|2B|2C|3B|3D|3A|40)/gi,
                map: {"%24": "$", "%26": "&", "%2B": "+", "%2C": ",", "%3B": ";", "%3D": "=", "%3A": ":", "%40": "@"}
            }, decode: {expression: /[\/\?#]/g, map: {"/": "%2F", "?": "%3F", "#": "%23"}}
        },
        reserved: {
            encode: {
                expression: /%(21|23|24|26|27|28|29|2A|2B|2C|2F|3A|3B|3D|3F|40|5B|5D)/gi,
                map: {
                    "%3A": ":",
                    "%2F": "/",
                    "%3F": "?",
                    "%23": "#",
                    "%5B": "[",
                    "%5D": "]",
                    "%40": "@",
                    "%21": "!",
                    "%24": "$",
                    "%26": "&",
                    "%27": "'",
                    "%28": "(",
                    "%29": ")",
                    "%2A": "*",
                    "%2B": "+",
                    "%2C": ",",
                    "%3B": ";",
                    "%3D": "="
                }
            }
        },
        urnpath: {
            encode: {
                expression: /%(21|24|27|28|29|2A|2B|2C|3B|3D|40)/gi,
                map: {
                    "%21": "!",
                    "%24": "$",
                    "%27": "'",
                    "%28": "(",
                    "%29": ")",
                    "%2A": "*",
                    "%2B": "+",
                    "%2C": ",",
                    "%3B": ";",
                    "%3D": "=",
                    "%40": "@"
                }
            }, decode: {expression: /[\/\?#:]/g, map: {"/": "%2F", "?": "%3F", "#": "%23", ":": "%3A"}}
        }
    }, e.encodeQuery = function (a, b) {
        var c = e.encode(a + "");
        return void 0 === b && (b = e.escapeQuerySpace), b ? c.replace(/%20/g, "+") : c
    }, e.decodeQuery = function (a, b) {
        a += "", void 0 === b && (b = e.escapeQuerySpace);
        try {
            return e.decode(b ? a.replace(/\+/g, "%20") : a)
        } catch (c) {
            return a
        }
    };
    var t, u = {encode: "encode", decode: "decode"}, v = function (a, b) {
        return function (c) {
            try {
                return e[b](c + "").replace(e.characters[a][b].expression, function (c) {
                    return e.characters[a][b].map[c]
                })
            } catch (d) {
                return c
            }
        }
    };
    for (t in u) e[t + "PathSegment"] = v("pathname", u[t]), e[t + "UrnPathSegment"] = v("urnpath", u[t]);
    var w = function (a, b, c) {
        return function (d) {
            var f;
            f = c ? function (a) {
                return e[b](e[c](a))
            } : e[b];
            for (var g = (d + "").split(a), h = 0, i = g.length; i > h; h++) g[h] = f(g[h]);
            return g.join(a)
        }
    };
    e.decodePath = w("/", "decodePathSegment"), e.decodeUrnPath = w(":", "decodeUrnPathSegment"), e.recodePath = w("/", "encodePathSegment", "decode"), e.recodeUrnPath = w(":", "encodeUrnPathSegment", "decode"), e.encodeReserved = v("reserved", "encode"), e.parse = function (a, b) {
        var c;
        return b || (b = {}), c = a.indexOf("#"), c > -1 && (b.fragment = a.substring(c + 1) || null, a = a.substring(0, c)), c = a.indexOf("?"), c > -1 && (b.query = a.substring(c + 1) || null, a = a.substring(0, c)), "//" === a.substring(0, 2) ? (b.protocol = null, a = a.substring(2), a = e.parseAuthority(a, b)) : (c = a.indexOf(":"), c > -1 && (b.protocol = a.substring(0, c) || null, b.protocol && !b.protocol.match(e.protocol_expression) ? b.protocol = void 0 : "//" === a.substring(c + 1, c + 3) ? (a = a.substring(c + 3), a = e.parseAuthority(a, b)) : (a = a.substring(c + 1), b.urn = !0))), b.path = a, b
    }, e.parseHost = function (a, b) {
        a = a.replace(/\\/g, "/");
        var c, d, e = a.indexOf("/");
        if (-1 === e && (e = a.length), "[" === a.charAt(0)) c = a.indexOf("]"), b.hostname = a.substring(1, c) || null, b.port = a.substring(c + 2, e) || null, "/" === b.port && (b.port = null); else {
            var f = a.indexOf(":"), g = a.indexOf("/"), h = a.indexOf(":", f + 1);
            -1 !== h && (-1 === g || g > h) ? (b.hostname = a.substring(0, e) || null, b.port = null) : (d = a.substring(0, e).split(":"), b.hostname = d[0] || null, b.port = d[1] || null)
        }
        return b.hostname && "/" !== a.substring(e).charAt(0) && (e++, a = "/" + a), a.substring(e) || "/"
    }, e.parseAuthority = function (a, b) {
        return a = e.parseUserinfo(a, b), e.parseHost(a, b)
    }, e.parseUserinfo = function (a, b) {
        var c, d = a.indexOf("/"), f = a.lastIndexOf("@", d > -1 ? d : a.length - 1);
        return f > -1 && (-1 === d || d > f) ? (c = a.substring(0, f).split(":"), b.username = c[0] ? e.decode(c[0]) : null, c.shift(), b.password = c[0] ? e.decode(c.join(":")) : null, a = a.substring(f + 1)) : (b.username = null, b.password = null), a
    }, e.parseQuery = function (a, b) {
        if (!a) return {};
        if (a = a.replace(/&+/g, "&").replace(/^\?*&*|&+$/g, ""), !a) return {};
        for (var c, d, f, g = {}, h = a.split("&"), i = h.length, j = 0; i > j; j++) c = h[j].split("="), d = e.decodeQuery(c.shift(), b), f = c.length ? e.decodeQuery(c.join("="), b) : null, s.call(g, d) ? (("string" == typeof g[d] || null === g[d]) && (g[d] = [g[d]]), g[d].push(f)) : g[d] = f;
        return g
    }, e.build = function (a) {
        var b = "";
        return a.protocol && (b += a.protocol + ":"), a.urn || !b && !a.hostname || (b += "//"), b += e.buildAuthority(a) || "", "string" == typeof a.path && ("/" !== a.path.charAt(0) && "string" == typeof a.hostname && (b += "/"), b += a.path), "string" == typeof a.query && a.query && (b += "?" + a.query), "string" == typeof a.fragment && a.fragment && (b += "#" + a.fragment), b
    }, e.buildHost = function (a) {
        var b = "";
        return a.hostname ? (b += e.ip6_expression.test(a.hostname) ? "[" + a.hostname + "]" : a.hostname, a.port && (b += ":" + a.port), b) : ""
    }, e.buildAuthority = function (a) {
        return e.buildUserinfo(a) + e.buildHost(a)
    }, e.buildUserinfo = function (a) {
        var b = "";
        return a.username && (b += e.encode(a.username), a.password && (b += ":" + e.encode(a.password)), b += "@"), b
    }, e.buildQuery = function (a, b, c) {
        var d, f, g, i, j = "";
        for (f in a) if (s.call(a, f) && f) if (h(a[f])) for (d = {}, g = 0, i = a[f].length; i > g; g++) void 0 !== a[f][g] && void 0 === d[a[f][g] + ""] && (j += "&" + e.buildQueryParameter(f, a[f][g], c), b !== !0 && (d[a[f][g] + ""] = !0)); else void 0 !== a[f] && (j += "&" + e.buildQueryParameter(f, a[f], c));
        return j.substring(1)
    }, e.buildQueryParameter = function (a, b, c) {
        return e.encodeQuery(a, c) + (null !== b ? "=" + e.encodeQuery(b, c) : "")
    }, e.addQuery = function (a, b, c) {
        if ("object" == typeof b) for (var d in b) s.call(b, d) && e.addQuery(a, d, b[d]); else {
            if ("string" != typeof b) throw new TypeError("URI.addQuery() accepts an object, string as the name parameter");
            if (void 0 === a[b]) return void (a[b] = c);
            "string" == typeof a[b] && (a[b] = [a[b]]), h(c) || (c = [c]), a[b] = (a[b] || []).concat(c)
        }
    }, e.removeQuery = function (a, b, c) {
        var d, f, j;
        if (h(b)) for (d = 0, f = b.length; f > d; d++) a[b[d]] = void 0; else if ("RegExp" === g(b)) for (j in a) b.test(j) && (a[j] = void 0); else if ("object" == typeof b) for (j in b) s.call(b, j) && e.removeQuery(a, j, b[j]); else {
            if ("string" != typeof b) throw new TypeError("URI.removeQuery() accepts an object, string, RegExp as the first parameter");
            void 0 !== c ? "RegExp" === g(c) ? !h(a[b]) && c.test(a[b]) ? a[b] = void 0 : a[b] = i(a[b], c) : a[b] !== String(c) || h(c) && 1 !== c.length ? h(a[b]) && (a[b] = i(a[b], c)) : a[b] = void 0 : a[b] = void 0
        }
    }, e.hasQuery = function (a, b, c, d) {
        if ("object" == typeof b) {
            for (var f in b) if (s.call(b, f) && !e.hasQuery(a, f, b[f])) return !1;
            return !0
        }
        if ("string" != typeof b) throw new TypeError("URI.hasQuery() accepts an object, string as the name parameter");
        switch (g(c)) {
            case"Undefined":
                return b in a;
            case"Boolean":
                var i = Boolean(h(a[b]) ? a[b].length : a[b]);
                return c === i;
            case"Function":
                return !!c(a[b], b, a);
            case"Array":
                if (!h(a[b])) return !1;
                var l = d ? j : k;
                return l(a[b], c);
            case"RegExp":
                return h(a[b]) ? d ? j(a[b], c) : !1 : Boolean(a[b] && a[b].match(c));
            case"Number":
                c = String(c);
            case"String":
                return h(a[b]) ? d ? j(a[b], c) : !1 : a[b] === c;
            default:
                throw new TypeError("URI.hasQuery() accepts undefined, boolean, string, number, RegExp, Function as the value parameter")
        }
    }, e.commonPath = function (a, b) {
        var c, d = Math.min(a.length, b.length);
        for (c = 0; d > c; c++) if (a.charAt(c) !== b.charAt(c)) {
            c--;
            break
        }
        return 1 > c ? a.charAt(0) === b.charAt(0) && "/" === a.charAt(0) ? "/" : "" : (("/" !== a.charAt(c) || "/" !== b.charAt(c)) && (c = a.substring(0, c).lastIndexOf("/")), a.substring(0, c + 1))
    }, e.withinString = function (a, b, c) {
        c || (c = {});
        var d = c.start || e.findUri.start, f = c.end || e.findUri.end, g = c.trim || e.findUri.trim,
            h = /[a-z0-9-]=["']?$/i;
        for (d.lastIndex = 0; ;) {
            var i = d.exec(a);
            if (!i) break;
            var j = i.index;
            if (c.ignoreHtml) {
                var k = a.slice(Math.max(j - 3, 0), j);
                if (k && h.test(k)) continue
            }
            var l = j + a.slice(j).search(f), m = a.slice(j, l).replace(g, "");
            if (!c.ignore || !c.ignore.test(m)) {
                l = j + m.length;
                var n = b(m, j, l, a);
                a = a.slice(0, j) + n + a.slice(l), d.lastIndex = j + n.length
            }
        }
        return d.lastIndex = 0, a
    }, e.ensureValidHostname = function (b) {
        if (b.match(e.invalid_hostname_characters)) {
            if (!a) throw new TypeError('Hostname "' + b + '" contains characters other than [A-Z0-9.-] and Punycode.js is not available');
            if (a.toASCII(b).match(e.invalid_hostname_characters)) throw new TypeError('Hostname "' + b + '" contains characters other than [A-Z0-9.-]')
        }
    }, e.noConflict = function (a) {
        if (a) {
            var b = {URI: this.noConflict()};
            return d.URITemplate && "function" == typeof d.URITemplate.noConflict && (b.URITemplate = d.URITemplate.noConflict()), d.IPv6 && "function" == typeof d.IPv6.noConflict && (b.IPv6 = d.IPv6.noConflict()), d.SecondLevelDomains && "function" == typeof d.SecondLevelDomains.noConflict && (b.SecondLevelDomains = d.SecondLevelDomains.noConflict()), b
        }
        return d.URI === this && (d.URI = q), this
    }, r.build = function (a) {
        return a === !0 ? this._deferred_build = !0 : (void 0 === a || this._deferred_build) && (this._string = e.build(this._parts), this._deferred_build = !1), this
    }, r.clone = function () {
        return new e(this)
    }, r.valueOf = r.toString = function () {
        return this.build(!1)._string
    }, r.protocol = o("protocol"), r.username = o("username"), r.password = o("password"), r.hostname = o("hostname"), r.port = o("port"), r.query = p("query", "?"), r.fragment = p("fragment", "#"), r.search = function (a, b) {
        var c = this.query(a, b);
        return "string" == typeof c && c.length ? "?" + c : c
    }, r.hash = function (a, b) {
        var c = this.fragment(a, b);
        return "string" == typeof c && c.length ? "#" + c : c
    }, r.pathname = function (a, b) {
        if (void 0 === a || a === !0) {
            var c = this._parts.path || (this._parts.hostname ? "/" : "");
            return a ? (this._parts.urn ? e.decodeUrnPath : e.decodePath)(c) : c
        }
        return this._parts.urn ? this._parts.path = a ? e.recodeUrnPath(a) : "" : this._parts.path = a ? e.recodePath(a) : "/", this.build(!b), this
    }, r.path = r.pathname, r.href = function (a, b) {
        var c;
        if (void 0 === a) return this.toString();
        this._string = "", this._parts = e._parts();
        var d = a instanceof e, f = "object" == typeof a && (a.hostname || a.path || a.pathname);
        if (a.nodeName) {
            var g = e.getDomAttribute(a);
            a = a[g] || "", f = !1
        }
        if (!d && f && void 0 !== a.pathname && (a = a.toString()), "string" == typeof a || a instanceof String) this._parts = e.parse(String(a), this._parts); else {
            if (!d && !f) throw new TypeError("invalid input");
            var h = d ? a._parts : a;
            for (c in h) s.call(this._parts, c) && (this._parts[c] = h[c])
        }
        return this.build(!b), this
    }, r.is = function (a) {
        var b = !1, d = !1, f = !1, g = !1, h = !1, i = !1, j = !1, k = !this._parts.urn;
        switch (this._parts.hostname && (k = !1, d = e.ip4_expression.test(this._parts.hostname), f = e.ip6_expression.test(this._parts.hostname), b = d || f, g = !b, h = g && c && c.has(this._parts.hostname), i = g && e.idn_expression.test(this._parts.hostname), j = g && e.punycode_expression.test(this._parts.hostname)), a.toLowerCase()) {
            case"relative":
                return k;
            case"absolute":
                return !k;
            case"domain":
            case"name":
                return g;
            case"sld":
                return h;
            case"ip":
                return b;
            case"ip4":
            case"ipv4":
            case"inet4":
                return d;
            case"ip6":
            case"ipv6":
            case"inet6":
                return f;
            case"idn":
                return i;
            case"url":
                return !this._parts.urn;
            case"urn":
                return !!this._parts.urn;
            case"punycode":
                return j
        }
        return null
    };
    var x = r.protocol, y = r.port, z = r.hostname;
    r.protocol = function (a, b) {
        if (void 0 !== a && a && (a = a.replace(/:(\/\/)?$/, ""), !a.match(e.protocol_expression))) throw new TypeError('Protocol "' + a + "\" contains characters other than [A-Z0-9.+-] or doesn't start with [A-Z]");
        return x.call(this, a, b)
    }, r.scheme = r.protocol, r.port = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 !== a && (0 === a && (a = null), a && (a += "", ":" === a.charAt(0) && (a = a.substring(1)), a.match(/[^0-9]/)))) throw new TypeError('Port "' + a + '" contains characters other than [0-9]');
        return y.call(this, a, b)
    }, r.hostname = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 !== a) {
            var c = {}, d = e.parseHost(a, c);
            if ("/" !== d) throw new TypeError('Hostname "' + a + '" contains characters other than [A-Z0-9.-]');
            a = c.hostname
        }
        return z.call(this, a, b)
    }, r.origin = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a) {
            var c = this.protocol(), d = this.authority();
            return d ? (c ? c + "://" : "") + this.authority() : ""
        }
        var f = e(a);
        return this.protocol(f.protocol()).authority(f.authority()).build(!b), this
    }, r.host = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a) return this._parts.hostname ? e.buildHost(this._parts) : "";
        var c = e.parseHost(a, this._parts);
        if ("/" !== c) throw new TypeError('Hostname "' + a + '" contains characters other than [A-Z0-9.-]');
        return this.build(!b), this
    }, r.authority = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a) return this._parts.hostname ? e.buildAuthority(this._parts) : "";
        var c = e.parseAuthority(a, this._parts);
        if ("/" !== c) throw new TypeError('Hostname "' + a + '" contains characters other than [A-Z0-9.-]');
        return this.build(!b), this
    }, r.userinfo = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a) {
            if (!this._parts.username) return "";
            var c = e.buildUserinfo(this._parts);
            return c.substring(0, c.length - 1)
        }
        return "@" !== a[a.length - 1] && (a += "@"), e.parseUserinfo(a, this._parts), this.build(!b), this
    }, r.resource = function (a, b) {
        var c;
        return void 0 === a ? this.path() + this.search() + this.hash() : (c = e.parse(a), this._parts.path = c.path, this._parts.query = c.query, this._parts.fragment = c.fragment, this.build(!b), this)
    }, r.subdomain = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a) {
            if (!this._parts.hostname || this.is("IP")) return "";
            var c = this._parts.hostname.length - this.domain().length - 1;
            return this._parts.hostname.substring(0, c) || ""
        }
        var d = this._parts.hostname.length - this.domain().length, g = this._parts.hostname.substring(0, d),
            h = new RegExp("^" + f(g));
        return a && "." !== a.charAt(a.length - 1) && (a += "."), a && e.ensureValidHostname(a), this._parts.hostname = this._parts.hostname.replace(h, a), this.build(!b), this
    }, r.domain = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if ("boolean" == typeof a && (b = a, a = void 0), void 0 === a) {
            if (!this._parts.hostname || this.is("IP")) return "";
            var c = this._parts.hostname.match(/\./g);
            if (c && c.length < 2) return this._parts.hostname;
            var d = this._parts.hostname.length - this.tld(b).length - 1;
            return d = this._parts.hostname.lastIndexOf(".", d - 1) + 1, this._parts.hostname.substring(d) || ""
        }
        if (!a) throw new TypeError("cannot set domain empty");
        if (e.ensureValidHostname(a), !this._parts.hostname || this.is("IP")) this._parts.hostname = a; else {
            var g = new RegExp(f(this.domain()) + "$");
            this._parts.hostname = this._parts.hostname.replace(g, a)
        }
        return this.build(!b), this
    }, r.tld = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if ("boolean" == typeof a && (b = a, a = void 0), void 0 === a) {
            if (!this._parts.hostname || this.is("IP")) return "";
            var d = this._parts.hostname.lastIndexOf("."), e = this._parts.hostname.substring(d + 1);
            return b !== !0 && c && c.list[e.toLowerCase()] ? c.get(this._parts.hostname) || e : e
        }
        var g;
        if (!a) throw new TypeError("cannot set TLD empty");
        if (a.match(/[^a-zA-Z0-9-]/)) {
            if (!c || !c.is(a)) throw new TypeError('TLD "' + a + '" contains characters other than [A-Z0-9]');
            g = new RegExp(f(this.tld()) + "$"), this._parts.hostname = this._parts.hostname.replace(g, a)
        } else {
            if (!this._parts.hostname || this.is("IP")) throw new ReferenceError("cannot set TLD on non-domain host");
            g = new RegExp(f(this.tld()) + "$"), this._parts.hostname = this._parts.hostname.replace(g, a)
        }
        return this.build(!b), this
    }, r.directory = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a || a === !0) {
            if (!this._parts.path && !this._parts.hostname) return "";
            if ("/" === this._parts.path) return "/";
            var c = this._parts.path.length - this.filename().length - 1,
                d = this._parts.path.substring(0, c) || (this._parts.hostname ? "/" : "");
            return a ? e.decodePath(d) : d
        }
        var g = this._parts.path.length - this.filename().length, h = this._parts.path.substring(0, g),
            i = new RegExp("^" + f(h));
        return this.is("relative") || (a || (a = "/"), "/" !== a.charAt(0) && (a = "/" + a)), a && "/" !== a.charAt(a.length - 1) && (a += "/"), a = e.recodePath(a), this._parts.path = this._parts.path.replace(i, a), this.build(!b), this
    }, r.filename = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a || a === !0) {
            if (!this._parts.path || "/" === this._parts.path) return "";
            var c = this._parts.path.lastIndexOf("/"), d = this._parts.path.substring(c + 1);
            return a ? e.decodePathSegment(d) : d
        }
        var g = !1;
        "/" === a.charAt(0) && (a = a.substring(1)), a.match(/\.?\//) && (g = !0);
        var h = new RegExp(f(this.filename()) + "$");
        return a = e.recodePath(a), this._parts.path = this._parts.path.replace(h, a), g ? this.normalizePath(b) : this.build(!b), this
    }, r.suffix = function (a, b) {
        if (this._parts.urn) return void 0 === a ? "" : this;
        if (void 0 === a || a === !0) {
            if (!this._parts.path || "/" === this._parts.path) return "";
            var c, d, g = this.filename(), h = g.lastIndexOf(".");
            return -1 === h ? "" : (c = g.substring(h + 1), d = /^[a-z0-9%]+$/i.test(c) ? c : "", a ? e.decodePathSegment(d) : d)
        }
        "." === a.charAt(0) && (a = a.substring(1));
        var i, j = this.suffix();
        if (j) i = a ? new RegExp(f(j) + "$") : new RegExp(f("." + j) + "$"); else {
            if (!a) return this;
            this._parts.path += "." + e.recodePath(a)
        }
        return i && (a = e.recodePath(a), this._parts.path = this._parts.path.replace(i, a)), this.build(!b), this
    }, r.segment = function (a, b, c) {
        var d = this._parts.urn ? ":" : "/", e = this.path(), f = "/" === e.substring(0, 1), g = e.split(d);
        if (void 0 !== a && "number" != typeof a && (c = b, b = a, a = void 0), void 0 !== a && "number" != typeof a) throw new Error('Bad segment "' + a + '", must be 0-based integer');
        if (f && g.shift(), 0 > a && (a = Math.max(g.length + a, 0)), void 0 === b) return void 0 === a ? g : g[a];
        if (null === a || void 0 === g[a]) if (h(b)) {
            g = [];
            for (var i = 0, j = b.length; j > i; i++) (b[i].length || g.length && g[g.length - 1].length) && (g.length && !g[g.length - 1].length && g.pop(), g.push(l(b[i])))
        } else (b || "string" == typeof b) && (b = l(b), "" === g[g.length - 1] ? g[g.length - 1] = b : g.push(b)); else b ? g[a] = l(b) : g.splice(a, 1);
        return f && g.unshift(""), this.path(g.join(d), c)
    }, r.segmentCoded = function (a, b, c) {
        var d, f, g;
        if ("number" != typeof a && (c = b, b = a, a = void 0), void 0 === b) {
            if (d = this.segment(a, b, c), h(d)) for (f = 0, g = d.length; g > f; f++) d[f] = e.decode(d[f]); else d = void 0 !== d ? e.decode(d) : void 0;
            return d
        }
        if (h(b)) for (f = 0, g = b.length; g > f; f++) b[f] = e.encode(b[f]); else b = "string" == typeof b || b instanceof String ? e.encode(b) : b;
        return this.segment(a, b, c)
    };
    var A = r.query;
    return r.query = function (a, b) {
        if (a === !0) return e.parseQuery(this._parts.query, this._parts.escapeQuerySpace);
        if ("function" == typeof a) {
            var c = e.parseQuery(this._parts.query, this._parts.escapeQuerySpace), d = a.call(this, c);
            return this._parts.query = e.buildQuery(d || c, this._parts.duplicateQueryParameters, this._parts.escapeQuerySpace), this.build(!b), this
        }
        return void 0 !== a && "string" != typeof a ? (this._parts.query = e.buildQuery(a, this._parts.duplicateQueryParameters, this._parts.escapeQuerySpace), this.build(!b), this) : A.call(this, a, b)
    }, r.setQuery = function (a, b, c) {
        var d = e.parseQuery(this._parts.query, this._parts.escapeQuerySpace);
        if ("string" == typeof a || a instanceof String) d[a] = void 0 !== b ? b : null; else {
            if ("object" != typeof a) throw new TypeError("URI.addQuery() accepts an object, string as the name parameter");
            for (var f in a) s.call(a, f) && (d[f] = a[f])
        }
        return this._parts.query = e.buildQuery(d, this._parts.duplicateQueryParameters, this._parts.escapeQuerySpace), "string" != typeof a && (c = b), this.build(!c), this
    }, r.addQuery = function (a, b, c) {
        var d = e.parseQuery(this._parts.query, this._parts.escapeQuerySpace);
        return e.addQuery(d, a, void 0 === b ? null : b), this._parts.query = e.buildQuery(d, this._parts.duplicateQueryParameters, this._parts.escapeQuerySpace), "string" != typeof a && (c = b), this.build(!c), this
    }, r.removeQuery = function (a, b, c) {
        var d = e.parseQuery(this._parts.query, this._parts.escapeQuerySpace);
        return e.removeQuery(d, a, b), this._parts.query = e.buildQuery(d, this._parts.duplicateQueryParameters, this._parts.escapeQuerySpace), "string" != typeof a && (c = b), this.build(!c), this
    }, r.hasQuery = function (a, b, c) {
        var d = e.parseQuery(this._parts.query, this._parts.escapeQuerySpace);
        return e.hasQuery(d, a, b, c)
    }, r.setSearch = r.setQuery, r.addSearch = r.addQuery, r.removeSearch = r.removeQuery, r.hasSearch = r.hasQuery, r.normalize = function () {
        return this._parts.urn ? this.normalizeProtocol(!1).normalizePath(!1).normalizeQuery(!1).normalizeFragment(!1).build() : this.normalizeProtocol(!1).normalizeHostname(!1).normalizePort(!1).normalizePath(!1).normalizeQuery(!1).normalizeFragment(!1).build()
    }, r.normalizeProtocol = function (a) {
        return "string" == typeof this._parts.protocol && (this._parts.protocol = this._parts.protocol.toLowerCase(), this.build(!a)), this
    }, r.normalizeHostname = function (c) {
        return this._parts.hostname && (this.is("IDN") && a ? this._parts.hostname = a.toASCII(this._parts.hostname) : this.is("IPv6") && b && (this._parts.hostname = b.best(this._parts.hostname)), this._parts.hostname = this._parts.hostname.toLowerCase(), this.build(!c)), this
    }, r.normalizePort = function (a) {
        return "string" == typeof this._parts.protocol && this._parts.port === e.defaultPorts[this._parts.protocol] && (this._parts.port = null, this.build(!a)), this
    }, r.normalizePath = function (a) {
        var b = this._parts.path;
        if (!b) return this;
        if (this._parts.urn) return this._parts.path = e.recodeUrnPath(this._parts.path), this.build(!a), this;
        if ("/" === this._parts.path) return this;
        var c, d, f, g = "";
        for ("/" !== b.charAt(0) && (c = !0, b = "/" + b), ("/.." === b.slice(-3) || "/." === b.slice(-2)) && (b += "/"), b = b.replace(/(\/(\.\/)+)|(\/\.$)/g, "/").replace(/\/{2,}/g, "/"), c && (g = b.substring(1).match(/^(\.\.\/)+/) || "", g && (g = g[0])); ;) {
            if (d = b.indexOf("/.."), -1 === d) break;
            0 !== d ? (f = b.substring(0, d).lastIndexOf("/"), -1 === f && (f = d), b = b.substring(0, f) + b.substring(d + 3)) : b = b.substring(3)
        }
        return c && this.is("relative") && (b = g + b.substring(1)), b = e.recodePath(b), this._parts.path = b, this.build(!a), this
    }, r.normalizePathname = r.normalizePath, r.normalizeQuery = function (a) {
        return "string" == typeof this._parts.query && (this._parts.query.length ? this.query(e.parseQuery(this._parts.query, this._parts.escapeQuerySpace)) : this._parts.query = null, this.build(!a)), this
    }, r.normalizeFragment = function (a) {
        return this._parts.fragment || (this._parts.fragment = null, this.build(!a)), this
    }, r.normalizeSearch = r.normalizeQuery, r.normalizeHash = r.normalizeFragment, r.iso8859 = function () {
        var a = e.encode, b = e.decode;
        e.encode = escape, e.decode = decodeURIComponent;
        try {
            this.normalize()
        } finally {
            e.encode = a, e.decode = b
        }
        return this
    }, r.unicode = function () {
        var a = e.encode, b = e.decode;
        e.encode = n, e.decode = unescape;
        try {
            this.normalize()
        } finally {
            e.encode = a, e.decode = b
        }
        return this
    }, r.readable = function () {
        var b = this.clone();
        b.username("").password("").normalize();
        var c = "";
        if (b._parts.protocol && (c += b._parts.protocol + "://"), b._parts.hostname && (b.is("punycode") && a ? (c += a.toUnicode(b._parts.hostname), b._parts.port && (c += ":" + b._parts.port)) : c += b.host()), b._parts.hostname && b._parts.path && "/" !== b._parts.path.charAt(0) && (c += "/"), c += b.path(!0), b._parts.query) {
            for (var d = "", f = 0, g = b._parts.query.split("&"), h = g.length; h > f; f++) {
                var i = (g[f] || "").split("=");
                d += "&" + e.decodeQuery(i[0], this._parts.escapeQuerySpace).replace(/&/g, "%26"), void 0 !== i[1] && (d += "=" + e.decodeQuery(i[1], this._parts.escapeQuerySpace).replace(/&/g, "%26"))
            }
            c += "?" + d.substring(1)
        }
        return c += e.decodeQuery(b.hash(), !0)
    }, r.absoluteTo = function (a) {
        var b, c, d, f = this.clone(), g = ["protocol", "username", "password", "hostname", "port"];
        if (this._parts.urn) throw new Error("URNs do not have any generally defined hierarchical components");
        if (a instanceof e || (a = new e(a)), f._parts.protocol || (f._parts.protocol = a._parts.protocol), this._parts.hostname) return f;
        for (c = 0; d = g[c]; c++) f._parts[d] = a._parts[d];
        return f._parts.path ? ".." === f._parts.path.substring(-2) && (f._parts.path += "/") : (f._parts.path = a._parts.path, f._parts.query || (f._parts.query = a._parts.query)), "/" !== f.path().charAt(0) && (b = a.directory(), b = b ? b : 0 === a.path().indexOf("/") ? "/" : "", f._parts.path = (b ? b + "/" : "") + f._parts.path, f.normalizePath()), f.build(), f
    }, r.relativeTo = function (a) {
        var b, c, d, f, g, h = this.clone().normalize();
        if (h._parts.urn) throw new Error("URNs do not have any generally defined hierarchical components");
        if (a = new e(a).normalize(), b = h._parts, c = a._parts, f = h.path(), g = a.path(), "/" !== f.charAt(0)) throw new Error("URI is already relative");
        if ("/" !== g.charAt(0)) throw new Error("Cannot calculate a URI relative to another relative URI");
        if (b.protocol === c.protocol && (b.protocol = null), b.username !== c.username || b.password !== c.password) return h.build();
        if (null !== b.protocol || null !== b.username || null !== b.password) return h.build();
        if (b.hostname !== c.hostname || b.port !== c.port) return h.build();
        if (b.hostname = null, b.port = null, f === g) return b.path = "", h.build();
        if (d = e.commonPath(f, g), !d) return h.build();
        var i = c.path.substring(d.length).replace(/[^\/]*$/, "").replace(/.*?\//g, "../");
        return b.path = i + b.path.substring(d.length) || "./", h.build()
    }, r.equals = function (a) {
        var b, c, d, f = this.clone(), g = new e(a), i = {}, j = {}, l = {};
        if (f.normalize(), g.normalize(), f.toString() === g.toString()) return !0;
        if (b = f.query(), c = g.query(), f.query(""), g.query(""), f.toString() !== g.toString()) return !1;
        if (b.length !== c.length) return !1;
        i = e.parseQuery(b, this._parts.escapeQuerySpace), j = e.parseQuery(c, this._parts.escapeQuerySpace);
        for (d in i) if (s.call(i, d)) {
            if (h(i[d])) {
                if (!k(i[d], j[d])) return !1
            } else if (i[d] !== j[d]) return !1;
            l[d] = !0
        }
        for (d in j) if (s.call(j, d) && !l[d]) return !1;
        return !0
    }, r.duplicateQueryParameters = function (a) {
        return this._parts.duplicateQueryParameters = !!a, this
    }, r.escapeQuerySpace = function (a) {
        return this._parts.escapeQuerySpace = !!a, this
    }, e
}), jQuery(function () {
    function a() {
        return $("meta[name=csrf-param]").attr("content")
    }

    function b() {
        return $("meta[name=csrf-token]").attr("content")
    }

    $.ajaxPrefilter(function (c, d, e) {
        !c.crossDomain && a() && e.setRequestHeader("X-CSRF-Token", b())
    }), $(document).ajaxError(function (a, b, c, d) {
        console.log("ajaxError"), console.log(d), "Found" != d && "abort" != d && "Permanent Redirect" != d && showStackError("Ошибка", "Возникла критическая ошибка")
    }), $(document).ajaxStart(function (a, b, c) {
        $("body").append('<div class="bg-loader"></div>')
    }), $(document).ajaxComplete(function (a, b, c) {
        console.log("ajaxComplete"), $(".bg-loader").remove();
        var d = b.getResponseHeader("X-Redirect");
        d && (window.location.href = d)
    }), initScriptFilter(), initPickers(!0), initSelectPicker(), $(document).on("shown.bs.modal", ".modal", function (a) {
        var b = $(this);
        if (!b.find(".modal-content").length) {
            var c = b.data("title") ? b.data("title") : "&nbsp;",
                d = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"></button> <h4 class="modal-title">' + c + "</h4> </div>";
            b.wrapInner('<div class="modal-content"></div>').wrapInner('<div class="modal-dialog"></div>'), b.find(".modal-content").wrapInner('<div class="modal-body"></div>'), b.find(".modal-content").prepend(d)
        }
    }), $(".agency .select-orange").on("change", function () {
        var a = $(this).find("option:selected").val();
        location.href = a
    }), $(".agency .select-pinkround").on("change", function () {
        var a = $(this).find("option:selected").val();
        location.href = a
    }), $(".agency .select-darkgray").on("change", function () {
        var a = $(this).find("option:selected").val();
        location.href = a
    }), $(".sticky-header").scrollToFixed({zIndex: 2001}), $(".deals").length && $(".deals").owlCarousel({
        autoWidth: !0,
        loop: !0,
        margin: 0,
        nav: !1,
        autoplay: !0,
        autoplayTimeout: 3e3,
        smartSpeed: 1e3,
        autoplayHoverPause: !1,
        navText: ["", ""],
        responsive: {0: {items: 1}, 600: {items: 3}, 1e3: {items: 5}}
    }), $(".payments-method").on("click", function (a) {
        a.preventDefault();
        var b = $(this);
        $(".payments-method").not(b).removeClass("active"), b.toggleClass("active"), $(".payments-form").not($(b.data("target"))).hide(), $(b.data("target")).slideToggle()
    }), $('a[href^="#"], span[data-href^="#"]').not('[data-toggle="tab"]').on("click", function (a) {
        var b = $(this), c = b.attr("href") ? $(b.attr("href")) : $(b.data("href")),
            d = b.data("extra") ? b.data("extra") : 0;
        c.length && $("html, body").animate({scrollTop: c.offset().top - d}, 500)
    });
    var c = $(".city-drop-down-list");
    c.length > 0 && c.autocomplete({
        serviceUrl: c.data("url"),
        paramName: "name",
        dataType: "JSON",
        minChars: 3,
        showNoSuggestionNotice: !0,
        noSuggestionNotice: "Город не найден",
        triggerSelectOnValidInput: !1,
        transformResult: function (a) {
            return {
                suggestions: $.map(a, function (a) {
                    return {
                        value: a.name,
                        city_id: a.city_id,
                        country: a.country,
                        has_metro: a.has_metro,
                        name: a.name_eng
                    }
                })
            }
        },
        formatResult: function (a, b) {
            return $targetButtonSelectCity = $("#button-select-city"), $targetButtonSelectCity.length && ($targetButtonSelectCity.data("city_id", null), $targetButtonSelectCity.data("city_name", null), $targetButtonSelectCity.parent().hide()), "<table class='category-item'><tr><td class='name'>" + a.value + "<div class='parent'>" + a.country + "</div></td></tr></table>"
        },
        onSelect: function (a) {
            var b = $("#button-select-city");
            b.length && (b.data("city_id", a.city_id), b.data("city_name", a.country + ", " + a.value), b.parent().show());
            var c = $("#select-city-change-form-action");
            if (c.length) {
                var d = c.attr("action"), e = new URI(d);
                e.subdomain();
                e.subdomain(a.name), c.attr("action", e.href())
            }
            var f = $("#metro-selection");
            f.length && (a.has_metro ? f.removeClass("hidden") : f.addClass("hidden"))
        }
    }), $(document).on("click", "#button-select-city", function (a) {
        a.preventDefault();
        var b = $(this);
        b.data("city_id") && ($("#" + b.data("target-city-id")).val(b.data("city_id")), $(b.data("target-select-city")).html(b.data("city_name")), $("#modal-select-city").modal("hide"))
    });
    var d = "/geo/metro-msk", e = $("#modal-subway-map"), f = $("#modal-subway-map .map"),
        g = $("#metro-selected-list"), h = [], i = null;
    $("#open-subway-map").on("click", function (a) {
        var b = ($(this), function () {
            g.find("p").each(function () {
                var a = $(this);
                $("#modal-subway-map").find("circle#" + a.attr("id")).attr("data-active", 1)
            })
        });
        return e.modal("show"), f.html('<div class="loader" style="margin: auto;"></div>'), e.find(".form-group").hide(), i ? (e.find(".form-group").show(), f.html(i), b()) : $.post(d, function (a) {
            i = a, e.find(".form-group").show(), f.html(i), b()
        }), !1
    }), $(document).on("mouseup", "#modal-subway-map .map circle.station, #modal-subway-map .map text.station", function () {
        var a = $(this);
        "text" == a.get(0).tagName && (a = a.parents("svg").find("circle#" + a.attr("id")));
        var b = 0;
        if (1 === parseInt(a.attr("data-active")) && (b = 1), b ? a.attr("data-active", 0) : a.attr("data-active", 1), g.length > 0) b ? (a.attr("data-active", 0), g.find("#" + a.attr("id")).remove()) : (a.attr("data-active", 1), g.append('<p id="' + a.attr("id") + '"> <span class="remove">X</span> ' + a.attr("title") + ' <input type="hidden" name="' + g.data("input-name") + '" value="' + getMetroId(a.attr("id")) + '" /></p>')); else {
            var c = getMetroId(a.attr("id"));
            b ? h.forEach(function (a, b, d) {
                return a[0] == c ? (h.splice(b, 1), !1) : void 0
            }) : h.push([c, a.attr("title")])
        }
        return i = a.parents("#modal-subway-map .map").html(), !1
    }), $(document).on("click", "#metro-selected-list .remove", function () {
        var a = $(this);
        return f.find("circle#" + a.parent("p").attr("id")).attr("data-active", "0"), a.parent("p").remove(), i = f.html(), !1
    }), $(document).on("click", "#metro-selected-list .remove", function () {
        var a = $(this);
        return f.find("circle#" + a.parent("p").attr("id")).attr("data-active", "0"), a.parent("p").remove(), i = f.html(), !1
    }), $(document).on("click", ".close-modal", function () {
        if ($(".modal").modal("hide"), g.length <= 0 && h.length > 0) {
            var a = [], b = [];
            h.forEach(function (c, d, e) {
                if (-1 == a.indexOf(c[0])) {
                    a.push(c[0]);
                    var f = toTranslit(c[1]);
                    f = f.split("-"), f = f[0] + "-" + f[1], b.push(c[0] + "_" + f)
                }
            });
            var c = $("#open-subway-map");
            c.length ? document.location.href = c.data("url") + "/?metro=" + b : document.location.href = "/?metro=" + b
        }
        return !1
    }), $(document).on("click", "#taxi", function () {
        return $.get("/msk-taxi", function (a) {
            $("#modal-taxi").remove(), $("body").append(a), $("#modal-taxi").modal("show")
        }), !1
    })
});
var reloadableScripts = [];
$.reject({
    reject: {all: !1, msie: 10},
    display: [],
    browserShow: !0,
    browserInfo: {
        chrome: {text: "Google Chrome", url: "http://www.google.com/chrome/"},
        firefox: {text: "Mozilla Firefox", url: "http://www.mozilla.com/firefox/"},
        safari: {text: "Safari", url: "http://www.apple.com/safari/download/"},
        opera: {text: "Opera", url: "http://www.opera.com/download/"}
    },
    header: "Did you know that your Internet Browser is out of date?",
    paragraph1: "Your browser is out of date, and may not be compatible with our website. A list of the most popular web browsers can be found below.",
    paragraph2: "Just click on the icons to get to the download page",
    close: !0,
    closeMessage: "By closing this window you acknowledge that your experience on this website may be degraded",
    closeLink: "Close This Window",
    closeURL: "#",
    closeESC: !0,
    closeCookie: !1,
    cookieSettings: {path: "/", expires: 0},
    imagePath: "./images/",
    overlayBgColor: "#000",
    overlayOpacity: .8,
    fadeInTime: "fast",
    fadeOutTime: "fast",
    analytics: !1
});


button.onclick = function () {
    var className = filter.className;
    if (className.indexOf(' expanded') == -1) {
        className += ' expanded';
    } else {
        className = className.replace(' expanded', '');
    }
    filter.className = className;
    return false;
};

$(window).scroll(function () {
    if ($(this).scrollTop() > 62) {
        $('#navigatio').addClass('fixed');
    } else if ($(this).scrollTop() < 62) {
        $('#navigatio').removeClass('fixed');
    }
});

$("#item-br").click(function () {
    $(".tutut").slideToggle();
    $(".tutut2").css('display', 'none');
});

$("#item-iu").click(function () {
    $(".tutut2").slideToggle();
    $(".tutut").css('display', 'none');
});
$("#item-iu-2").click(function () {
    $(".tutut").slideToggle();
});

$("#item-iu-3").click(function () {
    $(".tutut2").slideToggle();
});

$("#advert-btn").click(function () {
    $(".adverto-list").slideToggle();
});

$("#service-btn").click(function () {
    $(".servico-list").slideToggle();
});
 
 
