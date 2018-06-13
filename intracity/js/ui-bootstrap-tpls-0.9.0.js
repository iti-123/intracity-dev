angular.module("ui.bootstrap", ["ui.bootstrap.tpls", "ui.bootstrap.transition", "ui.bootstrap.collapse", "ui.bootstrap.accordion", "ui.bootstrap.alert", "ui.bootstrap.bindHtml", "ui.bootstrap.buttons", "ui.bootstrap.carousel", "ui.bootstrap.position", "ui.bootstrap.datepicker", "ui.bootstrap.dropdownToggle", "ui.bootstrap.modal", "ui.bootstrap.pagination", "ui.bootstrap.tooltip", "ui.bootstrap.popover", "ui.bootstrap.progressbar", "ui.bootstrap.rating", "ui.bootstrap.tabs", "ui.bootstrap.timepicker", "ui.bootstrap.typeahead"]), angular.module("ui.bootstrap.tpls", ["template/accordion/accordion-group.html", "template/accordion/accordion.html", "template/alert/alert.html", "template/carousel/carousel.html", "template/carousel/slide.html", "template/datepicker/datepicker.html", "template/datepicker/popup.html", "template/modal/backdrop.html", "template/modal/window.html", "template/pagination/pager.html", "template/pagination/pagination.html", "template/tooltip/tooltip-html-unsafe-popup.html", "template/tooltip/tooltip-popup.html", "template/popover/popover.html", "template/progressbar/bar.html", "template/progressbar/progress.html", "template/progressbar/progressbar.html", "template/rating/rating.html", "template/tabs/tab.html", "template/tabs/tabset.html", "template/timepicker/timepicker.html", "template/typeahead/typeahead-match.html", "template/typeahead/typeahead-popup.html"]), angular.module("ui.bootstrap.transition", []).factory("$transition", ["$q", "$timeout", "$rootScope", function (a, b, c) {
    function d(a) {
        for (var b in a)
            if (void 0 !== f.style[b]) return a[b]
    }

    var e = function (d, f, g) {
            g = g || {};
            var h = a.defer(),
                i = e[g.animation ? "animationEndEventName" : "transitionEndEventName"],
                j = function () {
                    c.$apply(function () {
                        d.unbind(i, j), h.resolve(d)
                    })
                };
            return i && d.bind(i, j), b(function () {
                angular.isString(f) ? d.addClass(f) : angular.isFunction(f) ? f(d) : angular.isObject(f) && d.css(f), i || h.resolve(d)
            }), h.promise.cancel = function () {
                i && d.unbind(i, j), h.reject("Transition cancelled")
            }, h.promise
        },
        f = document.createElement("trans"),
        g = {
            WebkitTransition: "webkitTransitionEnd",
            MozTransition: "transitionend",
            OTransition: "oTransitionEnd",
            transition: "transitionend"
        },
        h = {
            WebkitTransition: "webkitAnimationEnd",
            MozTransition: "animationend",
            OTransition: "oAnimationEnd",
            transition: "animationend"
        };
    return e.transitionEndEventName = d(g), e.animationEndEventName = d(h), e
}]), angular.module("ui.bootstrap.collapse", ["ui.bootstrap.transition"]).directive("collapse", ["$transition", function (a) {
    return {
        link: function (b, c, d) {
            function e(b) {
                function d() {
                    j === e && (j = void 0)
                }

                var e = a(c, b);
                return j && j.cancel(), j = e, e.then(d, d), e
            }

            function f() {
                k ? (k = !1, g()) : (c.removeClass("collapse").addClass("collapsing"), e({height: c[0].scrollHeight + "px"}).then(g))
            }

            function g() {
                c.removeClass("collapsing"), c.addClass("collapse in"), c.css({height: "auto"})
            }

            function h() {
                if (k) k = !1, i(), c.css({height: 0});
                else {
                    c.css({height: c[0].scrollHeight + "px"});
                    {
                        c[0].offsetWidth
                    }
                    c.removeClass("collapse in").addClass("collapsing"), e({height: 0}).then(i)
                }
            }

            function i() {
                c.removeClass("collapsing"), c.addClass("collapse")
            }

            var j, k = !0;
            b.$watch(d.collapse, function (a) {
                a ? h() : f()
            })
        }
    }
}]), angular.module("ui.bootstrap.accordion", ["ui.bootstrap.collapse"]).constant("accordionConfig", {closeOthers: !0}).controller("AccordionController", ["$scope", "$attrs", "accordionConfig", function (a, b, c) {
    this.groups = [], this.closeOthers = function (d) {
        var e = angular.isDefined(b.closeOthers) ? a.$eval(b.closeOthers) : c.closeOthers;
        e && angular.forEach(this.groups, function (a) {
            a !== d && (a.isOpen = !1)
        })
    }, this.addGroup = function (a) {
        var b = this;
        this.groups.push(a), a.$on("$destroy", function () {
            b.removeGroup(a)
        })
    }, this.removeGroup = function (a) {
        var b = this.groups.indexOf(a);
        -1 !== b && this.groups.splice(this.groups.indexOf(a), 1)
    }
}]).directive("accordion", function () {
    return {
        restrict: "EA",
        controller: "AccordionController",
        transclude: !0,
        replace: !1,
        templateUrl: "template/accordion/accordion.html"
    }
}).directive("accordionGroup", ["$parse", function (a) {
    return {
        require: "^accordion",
        restrict: "EA",
        transclude: !0,
        replace: !0,
        templateUrl: "template/accordion/accordion-group.html",
        scope: {heading: "@"},
        controller: function () {
            this.setHeading = function (a) {
                this.heading = a
            }
        },
        link: function (b, c, d, e) {
            var f, g;
            e.addGroup(b), b.isOpen = !1, d.isOpen && (f = a(d.isOpen), g = f.assign, b.$parent.$watch(f, function (a) {
                b.isOpen = !!a
            })), b.$watch("isOpen", function (a) {
                a && e.closeOthers(b), g && g(b.$parent, a)
            })
        }
    }
}]).directive("accordionHeading", function () {
    return {
        restrict: "EA",
        transclude: !0,
        template: "",
        replace: !0,
        require: "^accordionGroup",
        compile: function (a, b, c) {
            return function (a, b, d, e) {
                e.setHeading(c(a, function () {
                }))
            }
        }
    }
}).directive("accordionTransclude", function () {
    return {
        require: "^accordionGroup", link: function (a, b, c, d) {
            a.$watch(function () {
                return d[c.accordionTransclude]
            }, function (a) {
                a && (b.html(""), b.append(a))
            })
        }
    }
}), angular.module("ui.bootstrap.alert", []).controller("AlertController", ["$scope", "$attrs", function (a, b) {
    a.closeable = "close" in b
}]).directive("alert", function () {
    return {
        restrict: "EA",
        controller: "AlertController",
        templateUrl: "template/alert/alert.html",
        transclude: !0,
        replace: !0,
        scope: {type: "=", close: "&"}
    }
}), angular.module("ui.bootstrap.bindHtml", []).directive("bindHtmlUnsafe", function () {
    return function (a, b, c) {
        b.addClass("ng-binding").data("$binding", c.bindHtmlUnsafe), a.$watch(c.bindHtmlUnsafe, function (a) {
            b.html(a || "")
        })
    }
}), angular.module("ui.bootstrap.buttons", []).constant("buttonConfig", {
    activeClass: "active",
    toggleEvent: "click"
}).controller("ButtonsController", ["buttonConfig", function (a) {
    this.activeClass = a.activeClass || "active", this.toggleEvent = a.toggleEvent || "click"
}]).directive("btnRadio", function () {
    return {
        require: ["btnRadio", "ngModel"],
        controller: "ButtonsController",
        link: function (a, b, c, d) {
            var e = d[0],
                f = d[1];
            f.$render = function () {
                b.toggleClass(e.activeClass, angular.equals(f.$modelValue, a.$eval(c.btnRadio)))
            }, b.bind(e.toggleEvent, function () {
                b.hasClass(e.activeClass) || a.$apply(function () {
                    f.$setViewValue(a.$eval(c.btnRadio)), f.$render()
                })
            })
        }
    }
}).directive("btnCheckbox", function () {
    return {
        require: ["btnCheckbox", "ngModel"],
        controller: "ButtonsController",
        link: function (a, b, c, d) {
            function e() {
                return g(c.btnCheckboxTrue, !0)
            }

            function f() {
                return g(c.btnCheckboxFalse, !1)
            }

            function g(b, c) {
                var d = a.$eval(b);
                return angular.isDefined(d) ? d : c
            }

            var h = d[0],
                i = d[1];
            i.$render = function () {
                b.toggleClass(h.activeClass, angular.equals(i.$modelValue, e()))
            }, b.bind(h.toggleEvent, function () {
                a.$apply(function () {
                    i.$setViewValue(b.hasClass(h.activeClass) ? f() : e()), i.$render()
                })
            })
        }
    }
}), angular.module("ui.bootstrap.carousel", ["ui.bootstrap.transition"]).controller("CarouselController", ["$scope", "$timeout", "$transition", "$q", function (a, b, c) {
    function d() {
        e();
        var c = +a.interval;
        !isNaN(c) && c >= 0 && (g = b(f, c))
    }

    function e() {
        g && (b.cancel(g), g = null)
    }

    function f() {
        h ? (a.next(), d()) : a.pause()
    }

    var g, h, i = this,
        j = i.slides = [],
        k = -1;
    i.currentSlide = null;
    var l = !1;
    i.select = function (e, f) {
        function g() {
            if (!l) {
                if (i.currentSlide && angular.isString(f) && !a.noTransition && e.$element) {
                    e.$element.addClass(f);
                    {
                        e.$element[0].offsetWidth
                    }
                    angular.forEach(j, function (a) {
                        angular.extend(a, {direction: "", entering: !1, leaving: !1, active: !1})
                    }), angular.extend(e, {
                        direction: f,
                        active: !0,
                        entering: !0
                    }), angular.extend(i.currentSlide || {}, {
                        direction: f,
                        leaving: !0
                    }), a.$currentTransition = c(e.$element, {}),
                        function (b, c) {
                            a.$currentTransition.then(function () {
                                h(b, c)
                            }, function () {
                                h(b, c)
                            })
                        }(e, i.currentSlide)
                } else h(e, i.currentSlide);
                i.currentSlide = e, k = m, d()
            }
        }

        function h(b, c) {
            angular.extend(b, {
                direction: "",
                active: !0,
                leaving: !1,
                entering: !1
            }), angular.extend(c || {}, {
                direction: "",
                active: !1,
                leaving: !1,
                entering: !1
            }), a.$currentTransition = null
        }

        var m = j.indexOf(e);
        void 0 === f && (f = m > k ? "next" : "prev"), e && e !== i.currentSlide && (a.$currentTransition ? (a.$currentTransition.cancel(), b(g)) : g())
    }, a.$on("$destroy", function () {
        l = !0
    }), i.indexOfSlide = function (a) {
        return j.indexOf(a)
    }, a.next = function () {
        var b = (k + 1) % j.length;
        return a.$currentTransition ? void 0 : i.select(j[b], "next")
    }, a.prev = function () {
        var b = 0 > k - 1 ? j.length - 1 : k - 1;
        return a.$currentTransition ? void 0 : i.select(j[b], "prev")
    }, a.select = function (a) {
        i.select(a)
    }, a.isActive = function (a) {
        return i.currentSlide === a
    }, a.slides = function () {
        return j
    }, a.$watch("interval", d), a.$on("$destroy", e), a.play = function () {
        h || (h = !0, d())
    }, a.pause = function () {
        a.noPause || (h = !1, e())
    }, i.addSlide = function (b, c) {
        b.$element = c, j.push(b), 1 === j.length || b.active ? (i.select(j[j.length - 1]), 1 == j.length && a.play()) : b.active = !1
    }, i.removeSlide = function (a) {
        var b = j.indexOf(a);
        j.splice(b, 1), j.length > 0 && a.active ? b >= j.length ? i.select(j[b - 1]) : i.select(j[b]) : k > b && k--
    }
}]).directive("carousel", [function () {
    return {
        restrict: "EA",
        transclude: !0,
        replace: !0,
        controller: "CarouselController",
        require: "carousel",
        templateUrl: "template/carousel/carousel.html",
        scope: {interval: "=", noTransition: "=", noPause: "="}
    }
}]).directive("slide", ["$parse", function (a) {
    return {
        require: "^carousel",
        restrict: "EA",
        transclude: !0,
        replace: !0,
        templateUrl: "template/carousel/slide.html",
        scope: {},
        link: function (b, c, d, e) {
            if (d.active) {
                var f = a(d.active),
                    g = f.assign,
                    h = b.active = f(b.$parent);
                b.$watch(function () {
                    var a = f(b.$parent);
                    return a !== b.active && (a !== h ? h = b.active = a : g(b.$parent, a = h = b.active)), a
                })
            }
            e.addSlide(b, c), b.$on("$destroy", function () {
                e.removeSlide(b)
            }), b.$watch("active", function (a) {
                a && e.select(b)
            })
        }
    }
}]), angular.module("ui.bootstrap.position", []).factory("$position", ["$document", "$window", function (a, b) {
    function c(a, c) {
        return a.currentStyle ? a.currentStyle[c] : b.getComputedStyle ? b.getComputedStyle(a)[c] : a.style[c]
    }

    function d(a) {
        return "static" === (c(a, "position") || "static")
    }

    var e = function (b) {
        for (var c = a[0], e = b.offsetParent || c; e && e !== c && d(e);) e = e.offsetParent;
        return e || c
    };
    return {
        position: function (b) {
            var c = this.offset(b),
                d = {top: 0, left: 0},
                f = e(b[0]);
            f != a[0] && (d = this.offset(angular.element(f)), d.top += f.clientTop - f.scrollTop, d.left += f.clientLeft - f.scrollLeft);
            var g = b[0].getBoundingClientRect();
            return {
                width: g.width || b.prop("offsetWidth"),
                height: g.height || b.prop("offsetHeight"),
                top: c.top - d.top,
                left: c.left - d.left
            }
        },
        offset: function (c) {
            var d = c[0].getBoundingClientRect();
            return {
                width: d.width || c.prop("offsetWidth"),
                height: d.height || c.prop("offsetHeight"),
                top: d.top + (b.pageYOffset || a[0].body.scrollTop || a[0].documentElement.scrollTop),
                left: d.left + (b.pageXOffset || a[0].body.scrollLeft || a[0].documentElement.scrollLeft)
            }
        }
    }
}]), angular.module("ui.bootstrap.datepicker", ["ui.bootstrap.position"]).constant("datepickerConfig", {
    dayFormat: "dd",
    monthFormat: "MMMM",
    yearFormat: "yyyy",
    dayHeaderFormat: "EEE",
    dayTitleFormat: "MMMM yyyy",
    monthTitleFormat: "yyyy",
    showWeeks: !0,
    startingDay: 0,
    yearRange: 20,
    minDate: null,
    maxDate: null
}).controller("DatepickerController", ["$scope", "$attrs", "dateFilter", "datepickerConfig", function (a, b, c, d) {
    function e(b, c) {
        return angular.isDefined(b) ? a.$parent.$eval(b) : c
    }

    function f(a, b) {
        return new Date(a, b, 0).getDate()
    }

    function g(a, b) {
        for (var c = new Array(b), d = a, e = 0; b > e;) c[e++] = new Date(d), d.setDate(d.getDate() + 1);
        return c
    }

    function h(a, b, d, e) {
        return {date: a, label: c(a, b), selected: !!d, secondary: !!e}
    }

    var i = {
            day: e(b.dayFormat, d.dayFormat),
            month: e(b.monthFormat, d.monthFormat),
            year: e(b.yearFormat, d.yearFormat),
            dayHeader: e(b.dayHeaderFormat, d.dayHeaderFormat),
            dayTitle: e(b.dayTitleFormat, d.dayTitleFormat),
            monthTitle: e(b.monthTitleFormat, d.monthTitleFormat)
        },
        j = e(b.startingDay, d.startingDay),
        k = e(b.yearRange, d.yearRange);
    this.minDate = d.minDate ? new Date(d.minDate) : null, this.maxDate = d.maxDate ? new Date(d.maxDate) : null, this.modes = [{
        name: "day",
        getVisibleDates: function (a, b) {
            var d = a.getFullYear(),
                e = a.getMonth(),
                k = new Date(d, e, 1),
                l = j - k.getDay(),
                m = l > 0 ? 7 - l : -l,
                n = new Date(k),
                o = 0;
            m > 0 && (n.setDate(-m + 1), o += m), o += f(d, e + 1), o += (7 - o % 7) % 7;
            for (var p = g(n, o), q = new Array(7), r = 0; o > r; r++) {
                var s = new Date(p[r]);
                p[r] = h(s, i.day, b && b.getDate() === s.getDate() && b.getMonth() === s.getMonth() && b.getFullYear() === s.getFullYear(), s.getMonth() !== e)
            }
            for (var t = 0; 7 > t; t++) q[t] = c(p[t].date, i.dayHeader);
            return {objects: p, title: c(a, i.dayTitle), labels: q}
        },
        compare: function (a, b) {
            return new Date(a.getFullYear(), a.getMonth(), a.getDate()) - new Date(b.getFullYear(), b.getMonth(), b.getDate())
        },
        split: 7,
        step: {months: 1}
    }, {
        name: "month",
        getVisibleDates: function (a, b) {
            for (var d = new Array(12), e = a.getFullYear(), f = 0; 12 > f; f++) {
                var g = new Date(e, f, 1);
                d[f] = h(g, i.month, b && b.getMonth() === f && b.getFullYear() === e)
            }
            return {objects: d, title: c(a, i.monthTitle)}
        },
        compare: function (a, b) {
            return new Date(a.getFullYear(), a.getMonth()) - new Date(b.getFullYear(), b.getMonth())
        },
        split: 3,
        step: {years: 1}
    }, {
        name: "year",
        getVisibleDates: function (a, b) {
            for (var c = new Array(k), d = a.getFullYear(), e = parseInt((d - 1) / k, 10) * k + 1, f = 0; k > f; f++) {
                var g = new Date(e + f, 0, 1);
                c[f] = h(g, i.year, b && b.getFullYear() === g.getFullYear())
            }
            return {objects: c, title: [c[0].label, c[k - 1].label].join(" - ")}
        },
        compare: function (a, b) {
            return a.getFullYear() - b.getFullYear()
        },
        split: 5,
        step: {years: k}
    }], this.isDisabled = function (b, c) {
        var d = this.modes[c || 0];
        return this.minDate && d.compare(b, this.minDate) < 0 || this.maxDate && d.compare(b, this.maxDate) > 0 || a.dateDisabled && a.dateDisabled({
            date: b,
            mode: d.name
        })
    }
}]).directive("datepicker", ["dateFilter", "$parse", "datepickerConfig", "$log", function (a, b, c, d) {
    return {
        restrict: "EA",
        replace: !0,
        templateUrl: "template/datepicker/datepicker.html",
        scope: {dateDisabled: "&"},
        require: ["datepicker", "?^ngModel"],
        controller: "DatepickerController",
        link: function (a, e, f, g) {
            function h() {
                a.showWeekNumbers = 0 === o && q
            }

            function i(a, b) {
                for (var c = []; a.length > 0;) c.push(a.splice(0, b));
                return c
            }

            function j(b) {
                var c = null,
                    e = !0;
                n.$modelValue && (c = new Date(n.$modelValue), isNaN(c) ? (e = !1, d.error('Datepicker directive: "ng-model" value must be a Date object, a number of milliseconds since 01.01.1970 or a string representing an RFC2822 or ISO 8601 date.')) : b && (p = c)), n.$setValidity("date", e);
                var f = m.modes[o],
                    g = f.getVisibleDates(p, c);
                angular.forEach(g.objects, function (a) {
                    a.disabled = m.isDisabled(a.date, o)
                }), n.$setValidity("date-disabled", !c || !m.isDisabled(c)), a.rows = i(g.objects, f.split), a.labels = g.labels || [], a.title = g.title
            }

            function k(a) {
                o = a, h(), j()
            }

            function l(a) {
                var b = new Date(a);
                b.setDate(b.getDate() + 4 - (b.getDay() || 7));
                var c = b.getTime();
                return b.setMonth(0), b.setDate(1), Math.floor(Math.round((c - b) / 864e5) / 7) + 1
            }

            var m = g[0],
                n = g[1];
            if (n) {
                var o = 0,
                    p = new Date,
                    q = c.showWeeks;
                f.showWeeks ? a.$parent.$watch(b(f.showWeeks), function (a) {
                    q = !!a, h()
                }) : h(), f.min && a.$parent.$watch(b(f.min), function (a) {
                    m.minDate = a ? new Date(a) : null, j()
                }), f.max && a.$parent.$watch(b(f.max), function (a) {
                    m.maxDate = a ? new Date(a) : null, j()
                }), n.$render = function () {
                    j(!0)
                }, a.select = function (a) {
                    if (0 === o) {
                        var b = n.$modelValue ? new Date(n.$modelValue) : new Date(0, 0, 0, 0, 0, 0, 0);
                        b.setFullYear(a.getFullYear(), a.getMonth(), a.getDate()), n.$setViewValue(b), j(!0)
                    } else p = a, k(o - 1)
                }, a.move = function (a) {
                    var b = m.modes[o].step;
                    p.setMonth(p.getMonth() + a * (b.months || 0)), p.setFullYear(p.getFullYear() + a * (b.years || 0)), j()
                }, a.toggleMode = function () {
                    k((o + 1) % m.modes.length)
                }, a.getWeekNumber = function (b) {
                    return 0 === o && a.showWeekNumbers && 7 === b.length ? l(b[0].date) : null
                }
            }
        }
    }
}]).constant("datepickerPopupConfig", {
    dateFormat: "yyyy-MM-dd",
    currentText: "Today",
    toggleWeeksText: "Weeks",
    clearText: "Clear",
    closeText: "Done",
    closeOnDateSelection: !0,
    appendToBody: !1,
    showButtonBar: !0
}).directive("datepickerPopup", ["$compile", "$parse", "$document", "$position", "dateFilter", "datepickerPopupConfig", "datepickerConfig", function (a, b, c, d, e, f, g) {
    return {
        restrict: "EA",
        require: "ngModel",
        link: function (h, i, j, k) {
            function l(a) {
                u ? u(h, !!a) : q.isOpen = !!a
            }

            function m(a) {
                if (a) {
                    if (angular.isDate(a)) return k.$setValidity("date", !0), a;
                    if (angular.isString(a)) {
                        var b = new Date(a);
                        return isNaN(b) ? (k.$setValidity("date", !1), void 0) : (k.$setValidity("date", !0), b)
                    }
                    return k.$setValidity("date", !1), void 0
                }
                return k.$setValidity("date", !0), null
            }

            function n(a, c, d) {
                a && (h.$watch(b(a), function (a) {
                    q[c] = a
                }), y.attr(d || c, c))
            }

            function o() {
                q.position = s ? d.offset(i) : d.position(i), q.position.top = q.position.top + i.prop("offsetHeight")
            }

            var p, q = h.$new(),
                r = angular.isDefined(j.closeOnDateSelection) ? h.$eval(j.closeOnDateSelection) : f.closeOnDateSelection,
                s = angular.isDefined(j.datepickerAppendToBody) ? h.$eval(j.datepickerAppendToBody) : f.appendToBody;
            j.$observe("datepickerPopup", function (a) {
                p = a || f.dateFormat, k.$render()
            }), q.showButtonBar = angular.isDefined(j.showButtonBar) ? h.$eval(j.showButtonBar) : f.showButtonBar, h.$on("$destroy", function () {
                B.remove(), q.$destroy()
            }), j.$observe("currentText", function (a) {
                q.currentText = angular.isDefined(a) ? a : f.currentText
            }), j.$observe("toggleWeeksText", function (a) {
                q.toggleWeeksText = angular.isDefined(a) ? a : f.toggleWeeksText
            }), j.$observe("clearText", function (a) {
                q.clearText = angular.isDefined(a) ? a : f.clearText
            }), j.$observe("closeText", function (a) {
                q.closeText = angular.isDefined(a) ? a : f.closeText
            });
            var t, u;
            j.isOpen && (t = b(j.isOpen), u = t.assign, h.$watch(t, function (a) {
                q.isOpen = !!a
            })), q.isOpen = t ? t(h) : !1;
            var v = function (a) {
                    q.isOpen && a.target !== i[0] && q.$apply(function () {
                        l(!1)
                    })
                },
                w = function () {
                    q.$apply(function () {
                        l(!0)
                    })
                },
                x = angular.element("<div datepicker-popup-wrap><div datepicker></div></div>");
            x.attr({"ng-model": "date", "ng-change": "dateSelection()"});
            var y = angular.element(x.children()[0]);
            j.datepickerOptions && y.attr(angular.extend({}, h.$eval(j.datepickerOptions))), k.$parsers.unshift(m), q.dateSelection = function (a) {
                angular.isDefined(a) && (q.date = a), k.$setViewValue(q.date), k.$render(), r && l(!1)
            }, i.bind("input change keyup", function () {
                q.$apply(function () {
                    q.date = k.$modelValue
                })
            }), k.$render = function () {
                var a = k.$viewValue ? e(k.$viewValue, p) : "";
                i.val(a), q.date = k.$modelValue
            }, n(j.min, "min"), n(j.max, "max"), j.showWeeks ? n(j.showWeeks, "showWeeks", "show-weeks") : (q.showWeeks = g.showWeeks, y.attr("show-weeks", "showWeeks")), j.dateDisabled && y.attr("date-disabled", j.dateDisabled);
            var z = !1,
                A = !1;
            q.$watch("isOpen", function (a) {
                a ? (o(), c.bind("click", v), A && i.unbind("focus", w), i[0].focus(), z = !0) : (z && c.unbind("click", v), i.bind("focus", w), A = !0), u && u(h, a)
            }), q.today = function () {
                q.dateSelection(new Date)
            }, q.clear = function () {
                q.dateSelection(null)
            };
            var B = a(x)(q);
            s ? c.find("body").append(B) : i.after(B)
        }
    }
}]).directive("datepickerPopupWrap", function () {
    return {
        restrict: "EA",
        replace: !0,
        transclude: !0,
        templateUrl: "template/datepicker/popup.html",
        link: function (a, b) {
            b.bind("click", function (a) {
                a.preventDefault(), a.stopPropagation()
            })
        }
    }
}), angular.module("ui.bootstrap.dropdownToggle", []).directive("dropdownToggle", ["$document", "$location", function (a) {
    var b = null,
        c = angular.noop;
    return {
        restrict: "CA",
        link: function (d, e) {
            d.$watch("$location.path", function () {
                c()
            }), e.parent().bind("click", function () {
                c()
            }), e.bind("click", function (d) {
                var f = e === b;
                d.preventDefault(), d.stopPropagation(), b && c(), f || e.hasClass("disabled") || e.prop("disabled") || (e.parent().addClass("open"), b = e, c = function (d) {
                    d && (d.preventDefault(), d.stopPropagation()), a.unbind("click", c), e.parent().removeClass("open"), c = angular.noop, b = null
                }, a.bind("click", c))
            })
        }
    }
}]), angular.module("ui.bootstrap.modal", []).factory("$$stackedMap", function () {
    return {
        createNew: function () {
            var a = [];
            return {
                add: function (b, c) {
                    a.push({key: b, value: c})
                },
                get: function (b) {
                    for (var c = 0; c < a.length; c++)
                        if (b == a[c].key) return a[c]
                },
                keys: function () {
                    for (var b = [], c = 0; c < a.length; c++) b.push(a[c].key);
                    return b
                },
                top: function () {
                    return a[a.length - 1]
                },
                remove: function (b) {
                    for (var c = -1, d = 0; d < a.length; d++)
                        if (b == a[d].key) {
                            c = d;
                            break
                        }
                    return a.splice(c, 1)[0]
                },
                removeTop: function () {
                    return a.splice(a.length - 1, 1)[0]
                },
                length: function () {
                    return a.length
                }
            }
        }
    }
}).directive("modalBackdrop", ["$timeout", function (a) {
    return {
        restrict: "EA", replace: !0, templateUrl: "template/modal/backdrop.html", link: function (b) {
            b.animate = !1, a(function () {
                b.animate = !0
            })
        }
    }
}]).directive("modalWindow", ["$modalStack", "$timeout", function (a, b) {
    return {
        restrict: "EA",
        scope: {index: "@"},
        replace: !0,
        transclude: !0,
        templateUrl: "template/modal/window.html",
        link: function (c, d, e) {
            c.windowClass = e.windowClass || "", b(function () {
                c.animate = !0, d[0].focus()
            }), c.close = function (b) {
                var c = a.getTop();
                c && c.value.backdrop && "static" != c.value.backdrop && b.target === b.currentTarget && (b.preventDefault(), b.stopPropagation(), a.dismiss(c.key, "backdrop click"))
            }
        }
    }
}]).factory("$modalStack", ["$document", "$compile", "$rootScope", "$$stackedMap", function (a, b, c, d) {
    function e() {
        for (var a = -1, b = k.keys(), c = 0; c < b.length; c++) k.get(b[c]).value.backdrop && (a = c);
        return a
    }

    function f(b) {
        var c = a.find("body").eq(0),
            d = k.get(b).value;
        k.remove(b), d.modalDomEl.remove(), c.toggleClass(i, k.length() > 0), h && -1 == e() && (h.remove(), h = void 0), d.modalScope.$destroy()
    }

    var g, h, i = "modal-open",
        j = c.$new(!0),
        k = d.createNew(),
        l = {};
    return c.$watch(e, function (a) {
        j.index = a
    }), a.bind("keydown", function (a) {
        var b;
        27 === a.which && (b = k.top(), b && b.value.keyboard && c.$apply(function () {
            l.dismiss(b.key)
        }))
    }), l.open = function (c, d) {
        k.add(c, {deferred: d.deferred, modalScope: d.scope, backdrop: d.backdrop, keyboard: d.keyboard});
        var f = a.find("body").eq(0);
        e() >= 0 && !h && (g = angular.element("<div modal-backdrop></div>"), h = b(g)(j), f.append(h));
        var l = angular.element("<div modal-window></div>");
        l.attr("window-class", d.windowClass), l.attr("index", k.length() - 1), l.html(d.content);
        var m = b(l)(d.scope);
        k.top().value.modalDomEl = m, f.append(m), f.addClass(i)
    }, l.close = function (a, b) {
        var c = k.get(a).value;
        c && (c.deferred.resolve(b), f(a))
    }, l.dismiss = function (a, b) {
        var c = k.get(a).value;
        c && (c.deferred.reject(b), f(a))
    }, l.getTop = function () {
        return k.top()
    }, l
}]).provider("$modal", function () {
    var a = {
        options: {backdrop: !0, keyboard: !0},
        $get: ["$injector", "$rootScope", "$q", "$http", "$templateCache", "$controller", "$modalStack", function (b, c, d, e, f, g, h) {
            function i(a) {
                return a.template ? d.when(a.template) : e.get(a.templateUrl, {cache: f}).then(function (a) {
                    return a.data
                })
            }

            function j(a) {
                var c = [];
                return angular.forEach(a, function (a) {
                    (angular.isFunction(a) || angular.isArray(a)) && c.push(d.when(b.invoke(a)))
                }), c
            }

            var k = {};
            return k.open = function (b) {
                var e = d.defer(),
                    f = d.defer(),
                    k = {
                        result: e.promise, opened: f.promise, close: function (a) {
                            h.close(k, a)
                        }, dismiss: function (a) {
                            h.dismiss(k, a)
                        }
                    };
                if (b = angular.extend({}, a.options, b), b.resolve = b.resolve || {}, !b.template && !b.templateUrl) throw new Error("One of template or templateUrl options is required.");
                var l = d.all([i(b)].concat(j(b.resolve)));
                return l.then(function (a) {
                    var d = (b.scope || c).$new();
                    d.$close = k.close, d.$dismiss = k.dismiss;
                    var f, i = {},
                        j = 1;
                    b.controller && (i.$scope = d, i.$modalInstance = k, angular.forEach(b.resolve, function (b, c) {
                        i[c] = a[j++]
                    }), f = g(b.controller, i)), h.open(k, {
                        scope: d,
                        deferred: e,
                        content: a[0],
                        backdrop: b.backdrop,
                        keyboard: b.keyboard,
                        windowClass: b.windowClass
                    })
                }, function (a) {
                    e.reject(a)
                }), l.then(function () {
                    f.resolve(!0)
                }, function () {
                    f.reject(!1)
                }), k
            }, k
        }]
    };
    return a
}), angular.module("ui.bootstrap.pagination", []).controller("PaginationController", ["$scope", "$attrs", "$parse", "$interpolate", function (a, b, c, d) {
    var e = this,
        f = b.numPages ? c(b.numPages).assign : angular.noop;
    this.init = function (d) {
        b.itemsPerPage ? a.$parent.$watch(c(b.itemsPerPage), function (b) {
            e.itemsPerPage = parseInt(b, 10), a.totalPages = e.calculateTotalPages()
        }) : this.itemsPerPage = d
    }, this.noPrevious = function () {
        return 1 === this.page
    }, this.noNext = function () {
        return this.page === a.totalPages
    }, this.isActive = function (a) {
        return this.page === a
    }, this.calculateTotalPages = function () {
        var b = this.itemsPerPage < 1 ? 1 : Math.ceil(a.totalItems / this.itemsPerPage);
        return Math.max(b || 0, 1)
    }, this.getAttributeValue = function (b, c, e) {
        return angular.isDefined(b) ? e ? d(b)(a.$parent) : a.$parent.$eval(b) : c
    }, this.render = function () {
        this.page = parseInt(a.page, 10) || 1, this.page > 0 && this.page <= a.totalPages && (a.pages = this.getPages(this.page, a.totalPages))
    }, a.selectPage = function (b) {
        !e.isActive(b) && b > 0 && b <= a.totalPages && (a.page = b, a.onSelectPage({page: b}))
    }, a.$watch("page", function () {
        e.render()
    }), a.$watch("totalItems", function () {
        a.totalPages = e.calculateTotalPages()
    }), a.$watch("totalPages", function (b) {
        f(a.$parent, b), e.page > b ? a.selectPage(b) : e.render()
    })
}]).constant("paginationConfig", {
    itemsPerPage: 10,
    boundaryLinks: !1,
    directionLinks: !0,
    firstText: "First",
    previousText: "Previous",
    nextText: "Next",
    lastText: "Last",
    rotate: !0
}).directive("pagination", ["$parse", "paginationConfig", function (a, b) {
    return {
        restrict: "EA",
        scope: {page: "=", totalItems: "=", onSelectPage: " &"},
        controller: "PaginationController",
        templateUrl: "template/pagination/pagination.html",
        replace: !0,
        link: function (c, d, e, f) {
            function g(a, b, c, d) {
                return {number: a, text: b, active: c, disabled: d}
            }

            var h, i = f.getAttributeValue(e.boundaryLinks, b.boundaryLinks),
                j = f.getAttributeValue(e.directionLinks, b.directionLinks),
                k = f.getAttributeValue(e.firstText, b.firstText, !0),
                l = f.getAttributeValue(e.previousText, b.previousText, !0),
                m = f.getAttributeValue(e.nextText, b.nextText, !0),
                n = f.getAttributeValue(e.lastText, b.lastText, !0),
                o = f.getAttributeValue(e.rotate, b.rotate);
            f.init(b.itemsPerPage), e.maxSize && c.$parent.$watch(a(e.maxSize), function (a) {
                h = parseInt(a, 10), f.render()
            }), f.getPages = function (a, b) {
                var c = [],
                    d = 1,
                    e = b,
                    p = angular.isDefined(h) && b > h;
                p && (o ? (d = Math.max(a - Math.floor(h / 2), 1), e = d + h - 1, e > b && (e = b, d = e - h + 1)) : (d = (Math.ceil(a / h) - 1) * h + 1, e = Math.min(d + h - 1, b)));
                for (var q = d; e >= q; q++) {
                    var r = g(q, q, f.isActive(q), !1);
                    c.push(r)
                }
                if (p && !o) {
                    if (d > 1) {
                        var s = g(d - 1, "...", !1, !1);
                        c.unshift(s)
                    }
                    if (b > e) {
                        var t = g(e + 1, "...", !1, !1);
                        c.push(t)
                    }
                }
                if (j) {
                    var u = g(a - 1, l, !1, f.noPrevious());
                    c.unshift(u);
                    var v = g(a + 1, m, !1, f.noNext());
                    c.push(v)
                }
                if (i) {
                    var w = g(1, k, !1, f.noPrevious());
                    c.unshift(w);
                    var x = g(b, n, !1, f.noNext());
                    c.push(x)
                }
                return c
            }
        }
    }
}]).constant("pagerConfig", {
    itemsPerPage: 10,
    previousText: "« Previous",
    nextText: "Next »",
    align: !0
}).directive("pager", ["pagerConfig", function (a) {
    return {
        restrict: "EA",
        scope: {page: "=", totalItems: "=", onSelectPage: " &"},
        controller: "PaginationController",
        templateUrl: "template/pagination/pager.html",
        replace: !0,
        link: function (b, c, d, e) {
            function f(a, b, c, d, e) {
                return {number: a, text: b, disabled: c, previous: i && d, next: i && e}
            }

            var g = e.getAttributeValue(d.previousText, a.previousText, !0),
                h = e.getAttributeValue(d.nextText, a.nextText, !0),
                i = e.getAttributeValue(d.align, a.align);
            e.init(a.itemsPerPage), e.getPages = function (a) {
                return [f(a - 1, g, e.noPrevious(), !0, !1), f(a + 1, h, e.noNext(), !1, !0)]
            }
        }
    }
}]), angular.module("ui.bootstrap.tooltip", ["ui.bootstrap.position", "ui.bootstrap.bindHtml"]).provider("$tooltip", function () {
    function a(a) {
        var b = /[A-Z]/g,
            c = "-";
        return a.replace(b, function (a, b) {
            return (b ? c : "") + a.toLowerCase()
        })
    }

    var b = {placement: "top", animation: !0, popupDelay: 0},
        c = {mouseenter: "mouseleave", click: "click", focus: "blur"},
        d = {};
    this.options = function (a) {
        angular.extend(d, a)
    }, this.setTriggers = function (a) {
        angular.extend(c, a)
    }, this.$get = ["$window", "$compile", "$timeout", "$parse", "$document", "$position", "$interpolate", function (e, f, g, h, i, j, k) {
        return function (e, l, m) {
            function n(a) {
                var b = a || o.trigger || m,
                    d = c[b] || b;
                return {show: b, hide: d}
            }

            var o = angular.extend({}, b, d),
                p = a(e),
                q = k.startSymbol(),
                r = k.endSymbol(),
                s = "<div " + p + '-popup title="' + q + "tt_title" + r + '" content="' + q + "tt_content" + r + '" placement="' + q + "tt_placement" + r + '" animation="tt_animation" is-open="tt_isOpen"></div>';
            return {
                restrict: "EA",
                scope: !0,
                link: function (a, b, c) {
                    function d() {
                        a.tt_isOpen ? m() : k()
                    }

                    function k() {
                        (!y || a.$eval(c[l + "Enable"])) && (a.tt_popupDelay ? (t = g(p, a.tt_popupDelay), t.then(function (a) {
                            a()
                        })) : a.$apply(p)())
                    }

                    function m() {
                        a.$apply(function () {
                            q()
                        })
                    }

                    function p() {
                        return a.tt_content ? (r && g.cancel(r), u.css({
                            top: 0,
                            left: 0,
                            display: "block"
                        }), v ? i.find("body").append(u) : b.after(u), z(), a.tt_isOpen = !0, z) : angular.noop
                    }

                    function q() {
                        a.tt_isOpen = !1, g.cancel(t), a.tt_animation ? r = g(function () {
                            u.remove()
                        }, 500) : u.remove()
                    }

                    var r, t, u = f(s)(a),
                        v = angular.isDefined(o.appendToBody) ? o.appendToBody : !1,
                        w = n(void 0),
                        x = !1,
                        y = angular.isDefined(c[l + "Enable"]),
                        z = function () {
                            var c, d, e, f;
                            switch (c = v ? j.offset(b) : j.position(b), d = u.prop("offsetWidth"), e = u.prop("offsetHeight"), a.tt_placement) {
                                case "right":
                                    f = {top: c.top + c.height / 2 - e / 2, left: c.left + c.width};
                                    break;
                                case "bottom":
                                    f = {top: c.top + c.height, left: c.left + c.width / 2 - d / 2};
                                    break;
                                case "left":
                                    f = {top: c.top + c.height / 2 - e / 2, left: c.left - d};
                                    break;
                                default:
                                    f = {top: c.top - e, left: c.left + c.width / 2 - d / 2}
                            }
                            f.top += "px", f.left += "px", u.css(f)
                        };
                    a.tt_isOpen = !1, c.$observe(e, function (b) {
                        a.tt_content = b, !b && a.tt_isOpen && q()
                    }), c.$observe(l + "Title", function (b) {
                        a.tt_title = b
                    }), c.$observe(l + "Placement", function (b) {
                        a.tt_placement = angular.isDefined(b) ? b : o.placement
                    }), c.$observe(l + "PopupDelay", function (b) {
                        var c = parseInt(b, 10);
                        a.tt_popupDelay = isNaN(c) ? o.popupDelay : c
                    });
                    var A = function () {
                        x && (b.unbind(w.show, k), b.unbind(w.hide, m))
                    };
                    c.$observe(l + "Trigger", function (a) {
                        A(), w = n(a), w.show === w.hide ? b.bind(w.show, d) : (b.bind(w.show, k), b.bind(w.hide, m)), x = !0
                    });
                    var B = a.$eval(c[l + "Animation"]);
                    a.tt_animation = angular.isDefined(B) ? !!B : o.animation, c.$observe(l + "AppendToBody", function (b) {
                        v = angular.isDefined(b) ? h(b)(a) : v
                    }), v && a.$on("$locationChangeSuccess", function () {
                        a.tt_isOpen && q()
                    }), a.$on("$destroy", function () {
                        g.cancel(r), g.cancel(t), A(), u.remove(), u.unbind(), u = null
                    })
                }
            }
        }
    }]
}).directive("tooltipPopup", function () {
    return {
        restrict: "EA",
        replace: !0,
        scope: {content: "@", placement: "@", animation: "&", isOpen: "&"},
        templateUrl: "template/tooltip/tooltip-popup.html"
    }
}).directive("tooltip", ["$tooltip", function (a) {
    return a("tooltip", "tooltip", "mouseenter")
}]).directive("tooltipHtmlUnsafePopup", function () {
    return {
        restrict: "EA",
        replace: !0,
        scope: {content: "@", placement: "@", animation: "&", isOpen: "&"},
        templateUrl: "template/tooltip/tooltip-html-unsafe-popup.html"
    }
}).directive("tooltipHtmlUnsafe", ["$tooltip", function (a) {
    return a("tooltipHtmlUnsafe", "tooltip", "mouseenter")
}]), angular.module("ui.bootstrap.popover", ["ui.bootstrap.tooltip"]).directive("popoverPopup", function () {
    return {
        restrict: "EA",
        replace: !0,
        scope: {title: "@", content: "@", placement: "@", animation: "&", isOpen: "&"},
        templateUrl: "template/popover/popover.html"
    }
}).directive("popover", ["$compile", "$timeout", "$parse", "$window", "$tooltip", function (a, b, c, d, e) {
    return e("popover", "popover", "click")
}]), angular.module("ui.bootstrap.progressbar", ["ui.bootstrap.transition"]).constant("progressConfig", {
    animate: !0,
    max: 100
}).controller("ProgressController", ["$scope", "$attrs", "progressConfig", "$transition", function (a, b, c, d) {
    var e = this,
        f = [],
        g = angular.isDefined(b.max) ? a.$parent.$eval(b.max) : c.max,
        h = angular.isDefined(b.animate) ? a.$parent.$eval(b.animate) : c.animate;
    this.addBar = function (a, b) {
        var c = 0,
            d = a.$parent.$index;
        angular.isDefined(d) && f[d] && (c = f[d].value), f.push(a), this.update(b, a.value, c), a.$watch("value", function (a, c) {
            a !== c && e.update(b, a, c)
        }), a.$on("$destroy", function () {
            e.removeBar(a)
        })
    }, this.update = function (a, b, c) {
        var e = this.getPercentage(b);
        h ? (a.css("width", this.getPercentage(c) + "%"), d(a, {width: e + "%"})) : a.css({
            transition: "none",
            width: e + "%"
        })
    }, this.removeBar = function (a) {
        f.splice(f.indexOf(a), 1)
    }, this.getPercentage = function (a) {
        return Math.round(100 * a / g)
    }
}]).directive("progress", function () {
    return {
        restrict: "EA",
        replace: !0,
        transclude: !0,
        controller: "ProgressController",
        require: "progress",
        scope: {},
        template: '<div class="progress" ng-transclude></div>'
    }
}).directive("bar", function () {
    return {
        restrict: "EA",
        replace: !0,
        transclude: !0,
        require: "^progress",
        scope: {value: "=", type: "@"},
        templateUrl: "template/progressbar/bar.html",
        link: function (a, b, c, d) {
            d.addBar(a, b)
        }
    }
}).directive("progressbar", function () {
    return {
        restrict: "EA",
        replace: !0,
        transclude: !0,
        controller: "ProgressController",
        scope: {value: "=", type: "@"},
        templateUrl: "template/progressbar/progressbar.html",
        link: function (a, b, c, d) {
            d.addBar(a, angular.element(b.children()[0]))
        }
    }
}), angular.module("ui.bootstrap.rating", []).constant("ratingConfig", {
    max: 5,
    stateOn: null,
    stateOff: null
}).controller("RatingController", ["$scope", "$attrs", "$parse", "ratingConfig", function (a, b, c, d) {
    this.maxRange = angular.isDefined(b.max) ? a.$parent.$eval(b.max) : d.max, this.stateOn = angular.isDefined(b.stateOn) ? a.$parent.$eval(b.stateOn) : d.stateOn, this.stateOff = angular.isDefined(b.stateOff) ? a.$parent.$eval(b.stateOff) : d.stateOff, this.createRateObjects = function (a) {
        for (var b = {
            stateOn: this.stateOn,
            stateOff: this.stateOff
        }, c = 0, d = a.length; d > c; c++) a[c] = angular.extend({index: c}, b, a[c]);
        return a
    }, a.range = angular.isDefined(b.ratingStates) ? this.createRateObjects(angular.copy(a.$parent.$eval(b.ratingStates))) : this.createRateObjects(new Array(this.maxRange)), a.rate = function (b) {
        a.readonly || a.value === b || (a.value = b)
    }, a.enter = function (b) {
        a.readonly || (a.val = b), a.onHover({value: b})
    }, a.reset = function () {
        a.val = angular.copy(a.value), a.onLeave()
    }, a.$watch("value", function (b) {
        a.val = b
    }), a.readonly = !1, b.readonly && a.$parent.$watch(c(b.readonly), function (b) {
        a.readonly = !!b
    })
}]).directive("rating", function () {
    return {
        restrict: "EA",
        scope: {value: "=", onHover: "&", onLeave: "&"},
        controller: "RatingController",
        templateUrl: "template/rating/rating.html",
        replace: !0
    }
}), angular.module("ui.bootstrap.tabs", []).controller("TabsetController", ["$scope", function (a) {
    var b = this,
        c = b.tabs = a.tabs = [];
    b.select = function (a) {
        angular.forEach(c, function (a) {
            a.active = !1
        }), a.active = !0
    }, b.addTab = function (a) {
        c.push(a), (1 === c.length || a.active) && b.select(a)
    }, b.removeTab = function (a) {
        var d = c.indexOf(a);
        if (a.active && c.length > 1) {
            var e = d == c.length - 1 ? d - 1 : d + 1;
            b.select(c[e])
        }
        c.splice(d, 1)
    }
}]).directive("tabset", function () {
    return {
        restrict: "EA",
        transclude: !0,
        replace: !0,
        scope: {},
        controller: "TabsetController",
        templateUrl: "template/tabs/tabset.html",
        link: function (a, b, c) {
            a.vertical = angular.isDefined(c.vertical) ? a.$parent.$eval(c.vertical) : !1, a.justified = angular.isDefined(c.justified) ? a.$parent.$eval(c.justified) : !1, a.type = angular.isDefined(c.type) ? a.$parent.$eval(c.type) : "tabs"
        }
    }
}).directive("tab", ["$parse", function (a) {
    return {
        require: "^tabset",
        restrict: "EA",
        replace: !0,
        templateUrl: "template/tabs/tab.html",
        transclude: !0,
        scope: {heading: "@", onSelect: "&select", onDeselect: "&deselect"},
        controller: function () {
        },
        compile: function (b, c, d) {
            return function (b, c, e, f) {
                var g, h;
                e.active ? (g = a(e.active), h = g.assign, b.$parent.$watch(g, function (a, c) {
                    a !== c && (b.active = !!a)
                }), b.active = g(b.$parent)) : h = g = angular.noop, b.$watch("active", function (a) {
                    h(b.$parent, a), a ? (f.select(b), b.onSelect()) : b.onDeselect()
                }), b.disabled = !1, e.disabled && b.$parent.$watch(a(e.disabled), function (a) {
                    b.disabled = !!a
                }), b.select = function () {
                    b.disabled || (b.active = !0)
                }, f.addTab(b), b.$on("$destroy", function () {
                    f.removeTab(b)
                }), b.$transcludeFn = d
            }
        }
    }
}]).directive("tabHeadingTransclude", [function () {
    return {
        restrict: "A", require: "^tab", link: function (a, b) {
            a.$watch("headingElement", function (a) {
                a && (b.html(""), b.append(a))
            })
        }
    }
}]).directive("tabContentTransclude", function () {
    function a(a) {
        return a.tagName && (a.hasAttribute("tab-heading") || a.hasAttribute("data-tab-heading") || "tab-heading" === a.tagName.toLowerCase() || "data-tab-heading" === a.tagName.toLowerCase())
    }

    return {
        restrict: "A",
        require: "^tabset",
        link: function (b, c, d) {
            var e = b.$eval(d.tabContentTransclude);
            e.$transcludeFn(e.$parent, function (b) {
                angular.forEach(b, function (b) {
                    a(b) ? e.headingElement = b : c.append(b)
                })
            })
        }
    }
}), angular.module("ui.bootstrap.timepicker", []).constant("timepickerConfig", {
    hourStep: 1,
    minuteStep: 1,
    showMeridian: !0,
    meridians: null,
    readonlyInput: !1,
    mousewheel: !0
}).directive("timepicker", ["$parse", "$log", "timepickerConfig", "$locale", function (a, b, c, d) {
    return {
        restrict: "EA",
        require: "?^ngModel",
        replace: !0,
        scope: {},
        templateUrl: "template/timepicker/timepicker.html",
        link: function (e, f, g, h) {
            function i() {
                var a = parseInt(e.hours, 10),
                    b = e.showMeridian ? a > 0 && 13 > a : a >= 0 && 24 > a;
                return b ? (e.showMeridian && (12 === a && (a = 0), e.meridian === q[1] && (a += 12)), a) : void 0
            }

            function j() {
                var a = parseInt(e.minutes, 10);
                return a >= 0 && 60 > a ? a : void 0
            }

            function k(a) {
                return angular.isDefined(a) && a.toString().length < 2 ? "0" + a : a
            }

            function l(a) {
                m(), h.$setViewValue(new Date(p)), n(a)
            }

            function m() {
                h.$setValidity("time", !0), e.invalidHours = !1, e.invalidMinutes = !1
            }

            function n(a) {
                var b = p.getHours(),
                    c = p.getMinutes();
                e.showMeridian && (b = 0 === b || 12 === b ? 12 : b % 12), e.hours = "h" === a ? b : k(b), e.minutes = "m" === a ? c : k(c), e.meridian = p.getHours() < 12 ? q[0] : q[1]
            }

            function o(a) {
                var b = new Date(p.getTime() + 6e4 * a);
                p.setHours(b.getHours(), b.getMinutes()), l()
            }

            if (h) {
                var p = new Date,
                    q = angular.isDefined(g.meridians) ? e.$parent.$eval(g.meridians) : c.meridians || d.DATETIME_FORMATS.AMPMS,
                    r = c.hourStep;
                g.hourStep && e.$parent.$watch(a(g.hourStep), function (a) {
                    r = parseInt(a, 10)
                });
                var s = c.minuteStep;
                g.minuteStep && e.$parent.$watch(a(g.minuteStep), function (a) {
                    s = parseInt(a, 10)
                }), e.showMeridian = c.showMeridian, g.showMeridian && e.$parent.$watch(a(g.showMeridian), function (a) {
                    if (e.showMeridian = !!a, h.$error.time) {
                        var b = i(),
                            c = j();
                        angular.isDefined(b) && angular.isDefined(c) && (p.setHours(b), l())
                    } else n()
                });
                var t = f.find("input"),
                    u = t.eq(0),
                    v = t.eq(1),
                    w = angular.isDefined(g.mousewheel) ? e.$eval(g.mousewheel) : c.mousewheel;
                if (w) {
                    var x = function (a) {
                        a.originalEvent && (a = a.originalEvent);
                        var b = a.wheelDelta ? a.wheelDelta : -a.deltaY;
                        return a.detail || b > 0
                    };
                    u.bind("mousewheel wheel", function (a) {
                        e.$apply(x(a) ? e.incrementHours() : e.decrementHours()), a.preventDefault()
                    }), v.bind("mousewheel wheel", function (a) {
                        e.$apply(x(a) ? e.incrementMinutes() : e.decrementMinutes()), a.preventDefault()
                    })
                }
                if (e.readonlyInput = angular.isDefined(g.readonlyInput) ? e.$eval(g.readonlyInput) : c.readonlyInput, e.readonlyInput) e.updateHours = angular.noop, e.updateMinutes = angular.noop;
                else {
                    var y = function (a, b) {
                        h.$setViewValue(null), h.$setValidity("time", !1), angular.isDefined(a) && (e.invalidHours = a), angular.isDefined(b) && (e.invalidMinutes = b)
                    };
                    e.updateHours = function () {
                        var a = i();
                        angular.isDefined(a) ? (p.setHours(a), l("h")) : y(!0)
                    }, u.bind("blur", function () {
                        !e.validHours && e.hours < 10 && e.$apply(function () {
                            e.hours = k(e.hours)
                        })
                    }), e.updateMinutes = function () {
                        var a = j();
                        angular.isDefined(a) ? (p.setMinutes(a), l("m")) : y(void 0, !0)
                    }, v.bind("blur", function () {
                        !e.invalidMinutes && e.minutes < 10 && e.$apply(function () {
                            e.minutes = k(e.minutes)
                        })
                    })
                }
                h.$render = function () {
                    var a = h.$modelValue ? new Date(h.$modelValue) : null;
                    isNaN(a) ? (h.$setValidity("time", !1), b.error('Timepicker directive: "ng-model" value must be a Date object, a number of milliseconds since 01.01.1970 or a string representing an RFC2822 or ISO 8601 date.')) : (a && (p = a), m(), n())
                }, e.incrementHours = function () {
                    o(60 * r)
                }, e.decrementHours = function () {
                    o(60 * -r)
                }, e.incrementMinutes = function () {
                    o(s)
                }, e.decrementMinutes = function () {
                    o(-s)
                }, e.toggleMeridian = function () {
                    o(720 * (p.getHours() < 12 ? 1 : -1))
                }
            }
        }
    }
}]), angular.module("ui.bootstrap.typeahead", ["ui.bootstrap.position", "ui.bootstrap.bindHtml"]).factory("typeaheadParser", ["$parse", function (a) {
    var b = /^\s*(.*?)(?:\s+as\s+(.*?))?\s+for\s+(?:([\$\w][\$\w\d]*))\s+in\s+(.*)$/;
    return {
        parse: function (c) {
            var d = c.match(b);
            if (!d) throw new Error("Expected typeahead specification in form of '_modelValue_ (as _label_)? for _item_ in _collection_' but got '" + c + "'.");
            return {itemName: d[3], source: a(d[4]), viewMapper: a(d[2] || d[1]), modelMapper: a(d[1])}
        }
    }
}]).directive("typeahead", ["$compile", "$parse", "$q", "$timeout", "$document", "$position", "typeaheadParser", function (a, b, c, d, e, f, g) {
    var h = [9, 13, 27, 38, 40];
    return {
        require: "ngModel",
        link: function (i, j, k, l) {
            var m, n = i.$eval(k.typeaheadMinLength) || 1,
                o = i.$eval(k.typeaheadWaitMs) || 0,
                p = i.$eval(k.typeaheadEditable) !== !1,
                q = b(k.typeaheadLoading).assign || angular.noop,
                r = b(k.typeaheadOnSelect),
                s = k.typeaheadInputFormatter ? b(k.typeaheadInputFormatter) : void 0,
                t = k.typeaheadAppendToBody ? b(k.typeaheadAppendToBody) : !1,
                u = b(k.ngModel).assign,
                v = g.parse(k.typeahead),
                w = angular.element("<div typeahead-popup></div>");
            w.attr({
                matches: "matches",
                active: "activeIdx",
                select: "select(activeIdx)",
                query: "query",
                position: "position"
            }), angular.isDefined(k.typeaheadTemplateUrl) && w.attr("template-url", k.typeaheadTemplateUrl);
            var x = i.$new();
            i.$on("$destroy", function () {
                x.$destroy()
            });
            var y = function () {
                    x.matches = [], x.activeIdx = -1
                },
                z = function (a) {
                    var b = {$viewValue: a};
                    q(i, !0), c.when(v.source(i, b)).then(function (c) {
                        if (a === l.$viewValue && m) {
                            if (c.length > 0) {
                                x.activeIdx = 0, x.matches.length = 0;
                                for (var d = 0; d < c.length; d++) b[v.itemName] = c[d], x.matches.push({
                                    label: v.viewMapper(x, b),
                                    model: c[d]
                                });
                                x.query = a, x.position = t ? f.offset(j) : f.position(j), x.position.top = x.position.top + j.prop("offsetHeight")
                            } else y();
                            q(i, !1)
                        }
                    }, function () {
                        y(), q(i, !1)
                    })
                };
            y(), x.query = void 0;
            var A;
            l.$parsers.unshift(function (a) {
                return m = !0, a && a.length >= n ? o > 0 ? (A && d.cancel(A), A = d(function () {
                    z(a)
                }, o)) : z(a) : (q(i, !1), y()), p ? a : a ? (l.$setValidity("editable", !1), void 0) : (l.$setValidity("editable", !0), a)
            }), l.$formatters.push(function (a) {
                var b, c, d = {};
                return s ? (d.$model = a, s(i, d)) : (d[v.itemName] = a, b = v.viewMapper(i, d), d[v.itemName] = void 0, c = v.viewMapper(i, d), b !== c ? b : a)
            }), x.select = function (a) {
                var b, c, d = {};
                d[v.itemName] = c = x.matches[a].model, b = v.modelMapper(i, d), u(i, b), l.$setValidity("editable", !0), r(i, {
                    $item: c,
                    $model: b,
                    $label: v.viewMapper(i, d)
                }), y(), j[0].focus()
            }, j.bind("keydown", function (a) {
                0 !== x.matches.length && -1 !== h.indexOf(a.which) && (a.preventDefault(), 40 === a.which ? (x.activeIdx = (x.activeIdx + 1) % x.matches.length, x.$digest()) : 38 === a.which ? (x.activeIdx = (x.activeIdx ? x.activeIdx : x.matches.length) - 1, x.$digest()) : 13 === a.which || 9 === a.which ? x.$apply(function () {
                    x.select(x.activeIdx)
                }) : 27 === a.which && (a.stopPropagation(), y(), x.$digest()))
            }), j.bind("blur", function () {
                m = !1
            });
            var B = function (a) {
                j[0] !== a.target && (y(), x.$digest())
            };
            e.bind("click", B), i.$on("$destroy", function () {
                e.unbind("click", B)
            });
            var C = a(w)(x);
            t ? e.find("body").append(C) : j.after(C)
        }
    }
}]).directive("typeaheadPopup", function () {
    return {
        restrict: "EA",
        scope: {matches: "=", query: "=", active: "=", position: "=", select: "&"},
        replace: !0,
        templateUrl: "template/typeahead/typeahead-popup.html",
        link: function (a, b, c) {
            a.templateUrl = c.templateUrl, a.isOpen = function () {
                return a.matches.length > 0
            }, a.isActive = function (b) {
                return a.active == b
            }, a.selectActive = function (b) {
                a.active = b
            }, a.selectMatch = function (b) {
                a.select({activeIdx: b})
            }
        }
    }
}).directive("typeaheadMatch", ["$http", "$templateCache", "$compile", "$parse", function (a, b, c, d) {
    return {
        restrict: "EA",
        scope: {index: "=", match: "=", query: "="},
        link: function (e, f, g) {
            var h = d(g.templateUrl)(e.$parent) || "template/typeahead/typeahead-match.html";
            a.get(h, {cache: b}).success(function (a) {
                f.replaceWith(c(a.trim())(e))
            })
        }
    }
}]).filter("typeaheadHighlight", function () {
    function a(a) {
        return a.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1")
    }

    return function (b, c) {
        return c ? b.replace(new RegExp(a(c), "gi"), "<strong>$&</strong>") : b
    }
}), angular.module("template/accordion/accordion-group.html", []).run(["$templateCache", function (a) {
    a.put("template/accordion/accordion-group.html", '<div class="panel panel-default">\n  <div class="panel-heading">\n    <h4 class="panel-title">\n      <a class="accordion-toggle" ng-click="isOpen = !isOpen" accordion-transclude="heading">{{heading}}</a>\n    </h4>\n  </div>\n  <div class="panel-collapse" collapse="!isOpen">\n	  <div class="panel-body" ng-transclude></div>\n  </div>\n</div>')
}]), angular.module("template/accordion/accordion.html", []).run(["$templateCache", function (a) {
    a.put("template/accordion/accordion.html", '<div class="panel-group" ng-transclude></div>')
}]), angular.module("template/alert/alert.html", []).run(["$templateCache", function (a) {
    a.put("template/alert/alert.html", "<div class='alert' ng-class='\"alert-\" + (type || \"warning\")'>\n    <button ng-show='closeable' type='button' class='close' ng-click='close()'>&times;</button>\n    <div ng-transclude></div>\n</div>\n")
}]), angular.module("template/carousel/carousel.html", []).run(["$templateCache", function (a) {
    a.put("template/carousel/carousel.html", '<div ng-mouseenter="pause()" ng-mouseleave="play()" class="carousel">\n    <ol class="carousel-indicators" ng-show="slides().length > 1">\n        <li ng-repeat="slide in slides()" ng-class="{active: isActive(slide)}" ng-click="select(slide)"></li>\n    </ol>\n    <div class="carousel-inner" ng-transclude></div>\n    <a class="left carousel-control" ng-click="prev()" ng-show="slides().length > 1"><span class="icon-prev"></span></a>\n    <a class="right carousel-control" ng-click="next()" ng-show="slides().length > 1"><span class="icon-next"></span></a>\n</div>\n')
}]), angular.module("template/carousel/slide.html", []).run(["$templateCache", function (a) {
    a.put("template/carousel/slide.html", "<div ng-class=\"{\n    'active': leaving || (active && !entering),\n    'prev': (next || active) && direction=='prev',\n    'next': (next || active) && direction=='next',\n    'right': direction=='prev',\n    'left': direction=='next'\n  }\" class=\"item text-center\" ng-transclude></div>\n")
}]), angular.module("template/datepicker/datepicker.html", []).run(["$templateCache", function (a) {
    a.put("template/datepicker/datepicker.html", '<table>\n  <thead>\n    <tr>\n      <th><button type="button" class="btn btn-default btn-sm pull-left" ng-click="move(-1)"><i class="glyphicon glyphicon-chevron-left"></i></button></th>\n      <th colspan="{{rows[0].length - 2 + showWeekNumbers}}"><button type="button" class="btn btn-default btn-sm btn-block" ng-click="toggleMode()"><strong>{{title}}</strong></button></th>\n      <th><button type="button" class="btn btn-default btn-sm pull-right" ng-click="move(1)"><i class="glyphicon glyphicon-chevron-right"></i></button></th>\n    </tr>\n    <tr ng-show="labels.length > 0" class="h6">\n      <th ng-show="showWeekNumbers" class="text-center">#</th>\n      <th ng-repeat="label in labels" class="text-center">{{label}}</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr ng-repeat="row in rows">\n      <td ng-show="showWeekNumbers" class="text-center"><em>{{ getWeekNumber(row) }}</em></td>\n      <td ng-repeat="dt in row" class="text-center">\n        <button type="button" style="width:100%;" class="btn btn-default btn-sm" ng-class="{\'btn-info\': dt.selected}" ng-click="select(dt.date)" ng-disabled="dt.disabled"><span ng-class="{\'text-muted\': dt.secondary}">{{dt.label}}</span></button>\n      </td>\n    </tr>\n  </tbody>\n</table>\n')
}]), angular.module("template/datepicker/popup.html", []).run(["$templateCache", function (a) {
    a.put("template/datepicker/popup.html", "<ul class=\"dropdown-menu\" ng-style=\"{display: (isOpen && 'block') || 'none', top: position.top+'px', left: position.left+'px'}\">\n	<li ng-transclude></li>\n" + '	<li ng-show="showButtonBar" style="padding:10px 9px 2px">\n		<span class="btn-group">\n			<button type="button" class="btn btn-sm btn-info" ng-click="today()">{{currentText}}</button>\n			<button type="button" class="btn btn-sm btn-default" ng-click="showWeeks = ! showWeeks" ng-class="{active: showWeeks}">{{toggleWeeksText}}</button>\n			<button type="button" class="btn btn-sm btn-danger" ng-click="clear()">{{clearText}}</button>\n		</span>\n		<button type="button" class="btn btn-sm btn-success pull-right" ng-click="isOpen = false">{{closeText}}</button>\n	</li>\n</ul>\n')
}]), angular.module("template/modal/backdrop.html", []).run(["$templateCache", function (a) {
    a.put("template/modal/backdrop.html", '<div class="modal-backdrop fade" ng-class="{in: animate}" ng-style="{\'z-index\': 1040 + index*10}"></div>')
}]), angular.module("template/modal/window.html", []).run(["$templateCache", function (a) {
    a.put("template/modal/window.html", '<div tabindex="-1" class="modal fade {{ windowClass }}" ng-class="{in: animate}" ng-style="{\'z-index\': 1050 + index*10, display: \'block\'}" ng-click="close($event)">\n    <div class="modal-dialog"><div class="modal-content" ng-transclude></div></div>\n</div>')
}]), angular.module("template/pagination/pager.html", []).run(["$templateCache", function (a) {
    a.put("template/pagination/pager.html", '<ul class="pager">\n  <li ng-repeat="page in pages" ng-class="{disabled: page.disabled, previous: page.previous, next: page.next}"><a ng-click="selectPage(page.number)">{{page.text}}</a></li>\n</ul>')
}]), angular.module("template/pagination/pagination.html", []).run(["$templateCache", function (a) {
    a.put("template/pagination/pagination.html", '<ul class="pagination">\n  <li ng-repeat="page in pages" ng-class="{active: page.active, disabled: page.disabled}"><a ng-click="selectPage(page.number)">{{page.text}}</a></li>\n</ul>')
}]), angular.module("template/tooltip/tooltip-html-unsafe-popup.html", []).run(["$templateCache", function (a) {
    a.put("template/tooltip/tooltip-html-unsafe-popup.html", '<div class="tooltip {{placement}}" ng-class="{ in: isOpen(), fade: animation() }">\n  <div class="tooltip-arrow"></div>\n  <div class="tooltip-inner" bind-html-unsafe="content"></div>\n</div>\n')
}]), angular.module("template/tooltip/tooltip-popup.html", []).run(["$templateCache", function (a) {
    a.put("template/tooltip/tooltip-popup.html", '<div class="tooltip {{placement}}" ng-class="{ in: isOpen(), fade: animation() }">\n  <div class="tooltip-arrow"></div>\n  <div class="tooltip-inner" ng-bind="content"></div>\n</div>\n')
}]), angular.module("template/popover/popover.html", []).run(["$templateCache", function (a) {
    a.put("template/popover/popover.html", '<div class="popover {{placement}}" ng-class="{ in: isOpen(), fade: animation() }">\n  <div class="arrow"></div>\n\n  <div class="popover-inner">\n      <h3 class="popover-title" ng-bind="title" ng-show="title"></h3>\n      <div class="popover-content" ng-bind="content"></div>\n  </div>\n</div>\n')
}]), angular.module("template/progressbar/bar.html", []).run(["$templateCache", function (a) {
    a.put("template/progressbar/bar.html", '<div class="progress-bar" ng-class="type && \'progress-bar-\' + type" ng-transclude></div>')
}]), angular.module("template/progressbar/progress.html", []).run(["$templateCache", function (a) {
    a.put("template/progressbar/progress.html", '<div class="progress" ng-transclude></div>')
}]), angular.module("template/progressbar/progressbar.html", []).run(["$templateCache", function (a) {
    a.put("template/progressbar/progressbar.html", '<div class="progress"><div class="progress-bar" ng-class="type && \'progress-bar-\' + type" ng-transclude></div></div>')
}]), angular.module("template/rating/rating.html", []).run(["$templateCache", function (a) {
    a.put("template/rating/rating.html", '<span ng-mouseleave="reset()">\n    <i ng-repeat="r in range" ng-mouseenter="enter($index + 1)" ng-click="rate($index + 1)" class="glyphicon" ng-class="$index < val && (r.stateOn || \'glyphicon-star\') || (r.stateOff || \'glyphicon-star-empty\')"></i>\n</span>')
}]), angular.module("template/tabs/tab.html", []).run(["$templateCache", function (a) {
    a.put("template/tabs/tab.html", '<li ng-class="{active: active, disabled: disabled}">\n  <a ng-click="select()" tab-heading-transclude>{{heading}}</a>\n</li>\n')
}]), angular.module("template/tabs/tabset-titles.html", []).run(["$templateCache", function (a) {
    a.put("template/tabs/tabset-titles.html", "<ul class=\"nav {{type && 'nav-' + type}}\" ng-class=\"{'nav-stacked': vertical}\">\n</ul>\n")
}]), angular.module("template/tabs/tabset.html", []).run(["$templateCache", function (a) {
    a.put("template/tabs/tabset.html", '\n<div class="tabbable">\n  <ul class="nav {{type && \'nav-\' + type}}" ng-class="{\'nav-stacked\': vertical, \'nav-justified\': justified}" ng-transclude></ul>\n  <div class="tab-content">\n    <div class="tab-pane" \n         ng-repeat="tab in tabs" \n         ng-class="{active: tab.active}"\n         tab-content-transclude="tab">\n    </div>\n  </div>\n</div>\n')
}]), angular.module("template/timepicker/timepicker.html", []).run(["$templateCache", function (a) {
    a.put("template/timepicker/timepicker.html", '<table>\n	<tbody>\n		<tr class="text-center">\n			<td><a ng-click="incrementHours()" class="btn btn-link"><span class="glyphicon glyphicon-chevron-up"></span></a></td>\n			<td>&nbsp;</td>\n			<td><a ng-click="incrementMinutes()" class="btn btn-link"><span class="glyphicon glyphicon-chevron-up"></span></a></td>\n			<td ng-show="showMeridian"></td>\n		</tr>\n		<tr>\n			<td style="width:50px;" class="form-group" ng-class="{\'has-error\': invalidHours}">\n				<input type="text" ng-model="hours" ng-change="updateHours()" class="form-control text-center" ng-mousewheel="incrementHours()" ng-readonly="readonlyInput" maxlength="2">\n			</td>\n			<td>:</td>\n			<td style="width:50px;" class="form-group" ng-class="{\'has-error\': invalidMinutes}">\n				<input type="text" ng-model="minutes" ng-change="updateMinutes()" class="form-control text-center" ng-readonly="readonlyInput" maxlength="2">\n			</td>\n			<td ng-show="showMeridian"><button class="btn btn-default text-center" ng-click="toggleMeridian()">{{meridian}}</button></td>\n		</tr>\n		<tr class="text-center">\n			<td><a ng-click="decrementHours()" class="btn btn-link"><span class="glyphicon glyphicon-chevron-down"></span></a></td>\n			<td>&nbsp;</td>\n			<td><a ng-click="decrementMinutes()" class="btn btn-link"><span class="glyphicon glyphicon-chevron-down"></span></a></td>\n			<td ng-show="showMeridian"></td>\n		</tr>\n	</tbody>\n</table>\n')
}]), angular.module("template/typeahead/typeahead-match.html", []).run(["$templateCache", function (a) {
    a.put("template/typeahead/typeahead-match.html", '<a tabindex="-1" bind-html-unsafe="match.label | typeaheadHighlight:query"></a>')
}]), angular.module("template/typeahead/typeahead-popup.html", []).run(["$templateCache", function (a) {
    a.put("template/typeahead/typeahead-popup.html", "<ul class=\"dropdown-menu\" ng-style=\"{display: isOpen()&&'block' || 'none', top: position.top+'px', left: position.left+'px'}\">\n" + '    <li ng-repeat="match in matches" ng-class="{active: isActive($index) }" ng-mouseenter="selectActive($index)" ng-click="selectMatch($index)">\n        <div typeahead-match index="$index" match="match" query="query" template-url="templateUrl"></div>\n    </li>\n</ul>')
}]);