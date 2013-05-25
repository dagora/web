class QueryCtrl extends Monocle.Controller

  elements:
    "input"         : "txtSearch"
    ".box"          : "boxes"
    "#pie"          : "pies"

  events:
    "click button"    : "onSearch"
    "keypress input"  : "onKeyPress"

  constructor: ->
    super
    # TukTuk.Modal.show "modal"
    # TukTuk.Modal.loading()
    @boxes.removeClass "active"


  onSearch: (event) ->
    @render()

  onKeyPress: (event) ->
    @boxes.removeClass "active"
    @pies.html ""

  render: ->
    @boxes.addClass "active"

    # BAR
    mock = [
      ['Year', 'Sales'],
      ['Apr/20',  1000],
      ['xxx/XX',  1234],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170]
    ]
    query = __Model.Query.create
      title: "TITULO DE CONSULTA"
      source: "Publico.es"
      unit: "people"
      data: mock

    new __View.GraphBar model: query


    # PIE
    new __View.GraphPie model: title: "Percent.1", percent: 25
    new __View.GraphPie model: title: "Percent.2", percent: 75
    new __View.GraphPie model: title: "Percent.3", percent: 34
    new __View.GraphPie model: title: "Percent.4", percent: 17

$ ->
  __Controller.Query = new QueryCtrl "section"
