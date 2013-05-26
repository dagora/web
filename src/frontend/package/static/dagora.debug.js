(function() {

  window.Dagora = (function() {
    var SERVICE, api;
    SERVICE = " http://api.dagora.es/v1/";
    api = function(type, method, parameters) {
      var promise,
        _this = this;
      if (parameters == null) {
        parameters = {};
      }
      promise = new Hope.Promise();
      TukTuk.Modal.loading();
      $.ajax({
        url: SERVICE + method,
        type: type,
        data: parameters,
        dataType: 'json',
        success: function(response) {
          return _this._delayPromise(promise, null, response);
        },
        error: function(xhr, type, request) {
          return _this._delayPromise(promise, xhr, null);
        }
      });
      return promise;
    };
    return {
      _delayPromise: function(promise, error, result) {
        TukTuk.Modal.hide();
        return setTimeout(function() {
          return promise.done(error, result);
        }, 300);
      },
      api: api
    };
  })();

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __Model.Source = (function(_super) {

    __extends(Source, _super);

    function Source() {
      return Source.__super__.constructor.apply(this, arguments);
    }

    Source.fields("id", "title", "link", "unit", "created", "updated");

    return Source;

  })(Monocle.Model);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __Model.Stat = (function(_super) {

    __extends(Stat, _super);

    function Stat() {
      return Stat.__super__.constructor.apply(this, arguments);
    }

    Stat.fields("id", "title", "link", "unit", "unit", "data", "created", "updated");

    return Stat;

  })(Monocle.Model);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __View.GraphBar = (function(_super) {
    var instance;

    __extends(GraphBar, _super);

    instance = void 0;

    GraphBar.prototype.container = "section > article #bar";

    GraphBar.prototype.template = "<div data-graph=\"bar\">\n  <h4 class=\"text bold color theme uppercase\">PROGRESION EN {{unit}}</h4>\n  <div class=\"graph\"></div>\n</div>";

    function GraphBar() {
      var data, options, stat, _i, _len, _ref;
      GraphBar.__super__.constructor.apply(this, arguments);
      this.html(this.model);
      data = [["Año", this.model.unit]];
      _ref = this.model.data;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        stat = _ref[_i];
        data.push([stat.date, stat.value]);
      }
      data = google.visualization.arrayToDataTable(data);
      options = {
        animation: {
          duration: 1000,
          easing: "linear"
        },
        areaOpacity: 0.1,
        backgroundColor: "#ecf0f1",
        chartArea: {
          width: "100%",
          top: 20
        },
        colors: ["#bdc3c7"],
        fontName: "Oswald",
        fontSize: 12,
        legend: {
          position: 'none'
        },
        pointSize: 16,
        hAxis: {
          baselineColor: "#f00",
          textStyle: {
            color: "#aaa"
          }
        },
        vAxis: {
          gridlines: {
            color: "#ecf0f1",
            count: 0
          }
        },
        height: 232
      };
      this.instance = new google.visualization.ColumnChart(this.el.find(".graph").get(0));
      this.instance.draw(data, options);
    }

    return GraphBar;

  })(Monocle.View);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __View.GraphPie = (function(_super) {
    var instance;

    __extends(GraphPie, _super);

    instance = void 0;

    GraphPie.prototype.container = "section > article #pie";

    GraphPie.prototype.template = "<li class=\"margin-top\" data-graph=\"pie\">\n  <h4 class=\"text bold color theme uppercase\">{{title}}</h4>\n  <div class=\"graph\"></div>\n</li>";

    function GraphPie() {
      var data, options;
      GraphPie.__super__.constructor.apply(this, arguments);
      this.append(this.model);
      options = {
        colors: ["3498db", "#ddd"],
        legend: {
          position: 'none'
        },
        fontSize: 18,
        fontName: "Oswald",
        chartArea: {
          width: "75%",
          height: "75%",
          top: 0
        },
        tooltip: {
          trigger: "focus",
          showColorCode: true
        }
      };
      data = google.visualization.arrayToDataTable([['Value', 'Value'], [this.model.name, this.model.percent], ['Available', 100 - this.model.percent]]);
      this.instance = new google.visualization.PieChart(this.el.find(".graph").get(0));
      this.instance.draw(data, options);
    }

    return GraphPie;

  })(Monocle.View);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __View.SourceListItem = (function(_super) {

    __extends(SourceListItem, _super);

    SourceListItem.prototype.container = "[data-context=search] > table";

    SourceListItem.prototype.template = "<tr>\n  <td class=\"padding\">\n    <h5 class=\"text color theme\">{{title}}</h5>\n    <small class=\"text book\">{{link}}</small>\n  </td>\n  <td class=\"padding text align right book\">\n    <span class=\"icon calendar\"></span> {{created}}\n  </td>\n</tr>";

    SourceListItem.prototype.events = {
      "click": "onClick"
    };

    function SourceListItem() {
      SourceListItem.__super__.constructor.apply(this, arguments);
      this.append(this.model);
    }

    SourceListItem.prototype.onClick = function(event) {
      return this.url("" + window.location.hash + "/" + this.model.id);
    };

    return SourceListItem;

  })(Monocle.View);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __View.SourceOverview = (function(_super) {
    var instance;

    __extends(SourceOverview, _super);

    instance = void 0;

    SourceOverview.prototype.container = "section > article #overview";

    SourceOverview.prototype.template = "<h4 class=\"text bold uppercase\">{{title}}</h4>\n<ul class=\"margin\" data-tuktuk=\"totals\" id=\"overview\">\n    <li>\n        <span class=\"icon book\"></span>\n        <strong>{{data.length}}</strong>\n        <small>registros</small></li>\n    <li>\n        <strong>{{unit}}</strong>\n        <small>unidad</small>\n    </li>\n    <li>\n        <span class=\"icon dashboard\"></span>\n        <strong>{{progresion}}%</strong>\n        <small>progresion</small>\n    </li>\n</ul>";

    function SourceOverview() {
      var progresion;
      SourceOverview.__super__.constructor.apply(this, arguments);
      progresion = (this.model.data[this.model.data.length - 1].value * 100) / this.model.data[0].value;
      this.model.progresion = parseInt(progresion);
      this.html(this.model);
    }

    return SourceOverview;

  })(Monocle.View);

}).call(this);

(function() {
  var AddSourceCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  AddSourceCtrl = (function(_super) {

    __extends(AddSourceCtrl, _super);

    function AddSourceCtrl() {
      return AddSourceCtrl.__super__.constructor.apply(this, arguments);
    }

    AddSourceCtrl.prototype.elements = {
      "#title": "txtTitle",
      "#link": "txtLink",
      "#unit": "txtUnit",
      "#data": "txtData"
    };

    AddSourceCtrl.prototype.events = {
      "click [data-action=add]": "onAdd"
    };

    AddSourceCtrl.prototype.onAdd = function(event) {
      var parameters;
      parameters = {
        title: this.txtTitle.val(),
        link: this.txtLink.val(),
        unit: this.txtUnit.val(),
        data: this.txtData.val()
      };
      return Dagora.api("POST", "sources.json", parameters).then(function(error, response) {
        if (response) {
          return TukTuk.Modal.show("source_added");
        }
      });
    };

    return AddSourceCtrl;

  })(Monocle.Controller);

  __Controller.Add = new AddSourceCtrl("[data-tuktuk=modal]#add_source");

}).call(this);

(function() {
  var SearchCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  SearchCtrl = (function(_super) {

    __extends(SearchCtrl, _super);

    SearchCtrl.prototype.elements = {
      "input#txt-search": "txtSearch",
      "[data-context=search] > table": "results"
    };

    SearchCtrl.prototype.events = {
      "click header button": "search",
      "keypress input": "onSearch"
    };

    function SearchCtrl() {
      SearchCtrl.__super__.constructor.apply(this, arguments);
      __Model.Source.bind("create", this.bindSourceCreated);
    }

    SearchCtrl.prototype.bindSourceCreated = function(instance) {
      return new __View.SourceListItem({
        model: instance
      });
    };

    SearchCtrl.prototype.onSearch = function(event) {
      __Controller.Source.hide();
      if (event.keyCode === 13) {
        return this.search();
      }
    };

    SearchCtrl.prototype.fetch = function(value) {
      var _this = this;
      this.results.html("");
      return Dagora.api("GET", "sources.json", {
        s: value
      }).then(function(error, response) {
        var source, _i, _len, _ref, _results;
        if (response != null) {
          _ref = response.data.resultsList;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            source = _ref[_i];
            _results.push(__Model.Source.create(source));
          }
          return _results;
        } else {
          return alert("Algo ha ido mal");
        }
      });
    };

    SearchCtrl.prototype.search = function() {
      if (this.txtSearch.val()) {
        __Controller.Source.hide();
        return this.url(this.txtSearch.val());
      }
    };

    return SearchCtrl;

  })(Monocle.Controller);

  __Controller.Search = new SearchCtrl("section");

}).call(this);

(function() {
  var SourceCtrl,
    _this = this,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  SourceCtrl = (function(_super) {

    __extends(SourceCtrl, _super);

    SourceCtrl.prototype.elements = {
      ".box": "boxes",
      "#pie": "pies",
      "#updated": "updated"
    };

    function SourceCtrl() {
      var _this = this;
      this.bindStatCreated = function(instance) {
        return SourceCtrl.prototype.bindStatCreated.apply(_this, arguments);
      };
      SourceCtrl.__super__.constructor.apply(this, arguments);
      __Model.Stat.bind("create", this.bindStatCreated);
    }

    SourceCtrl.prototype.bindStatCreated = function(instance) {
      this.pies.html("");
      this.updated.html(instance.updated);
      new __View.SourceOverview({
        model: instance
      });
      new __View.GraphBar({
        model: instance
      });
      new __View.GraphPie({
        model: {
          title: "Dato 1",
          percent: 25
        }
      });
      new __View.GraphPie({
        model: {
          title: "Dato 2",
          percent: 75
        }
      });
      new __View.GraphPie({
        model: {
          title: "Dato 3",
          percent: 34
        }
      });
      new __View.GraphPie({
        model: {
          title: "Dato 4",
          percent: 17
        }
      });
      return this.boxes.addClass("active");
    };

    SourceCtrl.prototype.fetch = function(id) {
      var _this = this;
      __Model.Stat.destroyAll();
      return Dagora.api("GET", "sources/" + id + ".json").then(function(error, response) {
        if (response != null) {
          return __Model.Stat.create(response.data);
        }
      });
    };

    SourceCtrl.prototype.hide = function() {
      return this.boxes.removeClass("active");
    };

    return SourceCtrl;

  })(Monocle.Controller);

  __Controller.Source = new SourceCtrl("section article[data-context=source]");

}).call(this);

(function() {
  var UrlCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  UrlCtrl = (function(_super) {

    __extends(UrlCtrl, _super);

    UrlCtrl.prototype.elements = {
      "[data-context]": "context"
    };

    function UrlCtrl() {
      UrlCtrl.__super__.constructor.apply(this, arguments);
      this.routes({
        ":context/:id": this.source,
        ":context": this.search
      });
      Monocle.Route.listen();
      if (!window.location.hash) {
        this.search(null);
      }
    }

    UrlCtrl.prototype.search = function(parameters) {
      this._context("search");
      if (((parameters != null ? parameters.context : void 0) != null) && parameters.context !== "") {
        return __Controller.Search.fetch(parameters.context);
      }
    };

    UrlCtrl.prototype.source = function(parameters) {
      this._context("source");
      if (parameters) {
        return __Controller.Source.fetch(parameters.id);
      }
    };

    UrlCtrl.prototype._context = function(value) {
      return this.context.hide().siblings("[data-context=" + value + "]").show();
    };

    return UrlCtrl;

  })(Monocle.Controller);

  $(function() {
    return __Controller.Url = new UrlCtrl("body");
  });

}).call(this);
