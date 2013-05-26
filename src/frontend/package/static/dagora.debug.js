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
          TukTuk.Modal.hide();
          return promise.done(null, response);
        },
        error: function(xhr, type, request) {
          TukTuk.Modal.hide();
          return promise.done(xhr, null);
        }
      });
      return promise;
    };
    return {
      api: api
    };
  })();

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __Model.Query = (function(_super) {

    __extends(Query, _super);

    function Query() {
      return Query.__super__.constructor.apply(this, arguments);
    }

    Query.fields("id", "title", "link", "unit", "unit", "data");

    return Query;

  })(Monocle.Model);

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

  __View.GraphBar = (function(_super) {
    var instance;

    __extends(GraphBar, _super);

    instance = void 0;

    GraphBar.prototype.container = "section > article #bar";

    GraphBar.prototype.template = "<div data-graph=\"bar\">\n  <h4 class=\"text bold color theme uppercase\">{{title}} <span class=\"text book color default italic\">({{source}})</span></h4>\n  <div class=\"graph\"></div>\n</div>";

    function GraphBar() {
      var data, options;
      GraphBar.__super__.constructor.apply(this, arguments);
      this.html(this.model);
      options = {
        animation: {
          duration: 1000,
          easing: "linear"
        },
        areaOpacity: 0.1,
        backgroundColor: "#ecf0f1",
        chartArea: {
          width: "100%"
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
        width: "100%",
        height: 292
      };
      data = google.visualization.arrayToDataTable(this.model.data);
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

    GraphPie.prototype.template = "<li data-graph=\"pie\">\n  <h4 class=\"text bold color theme uppercase\">{{title}}</h4>\n  <div class=\"graph\"></div>\n</li>";

    function GraphPie() {
      var data, options;
      GraphPie.__super__.constructor.apply(this, arguments);
      this.append(this.model);
      options = {
        colors: ["3498db", "#ddd"],
        legend: {
          position: 'none'
        },
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
      console.error(this.model);
      return this.url("/" + this.model.id);
    };

    return SourceListItem;

  })(Monocle.View);

}).call(this);

(function() {
  var QueryCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  QueryCtrl = (function(_super) {
    var URL;

    __extends(QueryCtrl, _super);

    URL = "http://api.dagora.es/";

    QueryCtrl.prototype.elements = {
      "input": "txtSearch",
      ".box": "boxes",
      "#pie": "pies"
    };

    function QueryCtrl() {
      QueryCtrl.__super__.constructor.apply(this, arguments);
      this.boxes.removeClass("hidden");
    }

    QueryCtrl.prototype.onKeyPress = function(event) {
      this.boxes.removeClass("active");
      this.pies.html("");
      if (event.keyCode === 13) {
        return this.search();
      }
    };

    QueryCtrl.prototype.search = function() {
      var mock, query;
      TukTuk.Modal.loading();
      this.boxes.addClass("active");
      mock = [['Year', 'Sales'], ['Apr/20', 1000], ['xxx/XX', 1234], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170]];
      query = __Model.Query.create({
        title: "TITULO DE CONSULTA",
        source: "Publico.es",
        unit: "people",
        data: mock
      });
      new __View.GraphBar({
        model: query
      });
      new __View.GraphPie({
        model: {
          title: "Percent.1",
          percent: 25
        }
      });
      new __View.GraphPie({
        model: {
          title: "Percent.2",
          percent: 75
        }
      });
      new __View.GraphPie({
        model: {
          title: "Percent.3",
          percent: 34
        }
      });
      new __View.GraphPie({
        model: {
          title: "Percent.4",
          percent: 17
        }
      });
      return TukTuk.Modal.hide();
    };

    return QueryCtrl;

  })(Monocle.Controller);

  __Controller.Query = new QueryCtrl("section");

}).call(this);

(function() {
  var SourcesCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  SourcesCtrl = (function(_super) {

    __extends(SourcesCtrl, _super);

    SourcesCtrl.prototype.elements = {
      "input": "txtSearch",
      "[data-context=search] > table": "results"
    };

    SourcesCtrl.prototype.events = {
      "click button": "search",
      "keypress input": "onSearch"
    };

    function SourcesCtrl() {
      SourcesCtrl.__super__.constructor.apply(this, arguments);
      __Model.Source.bind("create", this.bindSourceCreated);
    }

    SourcesCtrl.prototype.bindSourceCreated = function(instance) {
      return new __View.SourceListItem({
        model: instance
      });
    };

    SourcesCtrl.prototype.onSearch = function(event) {
      if (event.keyCode === 13 && (this.txtSearch.val() != null)) {
        return this.url(this.txtSearch.val());
      }
    };

    SourcesCtrl.prototype.fetch = function(value) {
      var _this = this;
      console.log("fetch", value);
      this.results.html("");
      return Dagora.api("GET", "sources.json", {
        s: value
      }).then(function(error, sources) {
        var source, _i, _len, _results;
        if (sources != null) {
          _results = [];
          for (_i = 0, _len = sources.length; _i < _len; _i++) {
            source = sources[_i];
            _results.push(__Model.Source.create(source));
          }
          return _results;
        } else {
          return _this.mock();
        }
      });
    };

    SourcesCtrl.prototype.mock = function() {
      __Model.Source.create({
        id: "1",
        title: "t1",
        link: "u1",
        created: new Date(),
        updated: new Date()
      });
      __Model.Source.create({
        id: "2",
        title: "t2",
        link: "u2",
        created: new Date(),
        updated: new Date()
      });
      __Model.Source.create({
        id: "3",
        title: "t3",
        link: "u3",
        created: new Date(),
        updated: new Date()
      });
      return __Model.Source.create({
        id: "4",
        title: "t4",
        link: "u4",
        created: new Date(),
        updated: new Date()
      });
    };

    return SourcesCtrl;

  })(Monocle.Controller);

  __Controller.Sources = new SourcesCtrl("section");

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
        this.search();
      }
    }

    UrlCtrl.prototype.search = function(parameters) {
      this._context("search");
      if (parameters) {
        return __Controller.Sources.fetch(parameters.context);
      }
    };

    UrlCtrl.prototype.source = function() {
      this._context("source");
      return this;
    };

    UrlCtrl.prototype._context = function(value) {
      return this.context.hide().siblings("[data-context=" + value + "]").show();
    };

    return UrlCtrl;

  })(Monocle.Controller);

  $(function() {
    return __Controller.Url = new UrlCtrl("section");
  });

}).call(this);
