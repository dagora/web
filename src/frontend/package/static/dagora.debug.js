(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __Model.Query = (function(_super) {

    __extends(Query, _super);

    function Query() {
      return Query.__super__.constructor.apply(this, arguments);
    }

    Query.fields("title", "source", "unit", "data");

    return Query;

  })(Monocle.Model);

}).call(this);

(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  __View.GraphLine = (function(_super) {
    var instance;

    __extends(GraphLine, _super);

    instance = void 0;

    GraphLine.prototype.container = "section#results > article #line";

    GraphLine.prototype.template = "<div data-graph=\"line\">\n  <h4 class=\"text bold color theme\">{{title}} <span class=\"text book color default italic\">({{source}})</span></h4>\n  <div class=\"graph\"></div>\n</div>";

    function GraphLine() {
      var data, options;
      GraphLine.__super__.constructor.apply(this, arguments);
      this.html(this.model);
      options = {
        animation: {
          duration: 1000,
          easing: "linear"
        },
        areaOpacity: 0.1,
        backgroundColor: "#ecf0f1",
        colors: ["#bdc3c7", "#666"],
        fontName: "Oswald",
        legend: {
          position: 'none'
        },
        pointSize: 16,
        vAxis: {
          gridlines: {
            color: "#ddd",
            count: 0
          }
        }
      };
      data = google.visualization.arrayToDataTable(this.model.data);
      this.instance = new google.visualization.ColumnChart(this.el.find(".graph").get(0));
      this.instance.draw(data, options);
    }

    return GraphLine;

  })(Monocle.View);

}).call(this);

(function() {
  var QueryCtrl,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  QueryCtrl = (function(_super) {

    __extends(QueryCtrl, _super);

    function QueryCtrl() {
      var mock, query;
      QueryCtrl.__super__.constructor.apply(this, arguments);
      console.error("hello world :)");
      mock = [['Year', 'Sales'], ['Apr/20', 1000], ['xxx/XX', 1234], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170], ['xxx/XX', 1170]];
      query = __Model.Query.create({
        title: "TITULO DE CONSULTA",
        source: "Publico.es",
        unit: "people",
        data: mock
      });
      new __View.GraphLine({
        model: query
      });
    }

    return QueryCtrl;

  })(Monocle.Controller);

  $(function() {
    return __Controller.Query = new QueryCtrl("body");
  });

}).call(this);
