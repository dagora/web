class SourceCtrl extends Monocle.Controller

  URL = "http://api.dagora.es/"

  elements:
    ".box"          : "boxes"
    "#pie"          : "pies"

  constructor: ->
    super
    @boxes.removeClass "hidden"

  fetch: (id) ->
    Dagora.api("GET", "sources/#{id}").then (error, source) =>
      if source?
        console.error "ss"
      else
        @mock()
        # console.error "ERROR: ", error

  mock: ->
    TukTuk.Modal.loading()
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

    # BAR
    new __View.GraphBar model: query

    # PIE
    new __View.GraphPie model: title: "Percent.1", percent: 25
    new __View.GraphPie model: title: "Percent.2", percent: 75
    new __View.GraphPie model: title: "Percent.3", percent: 34
    new __View.GraphPie model: title: "Percent.4", percent: 17

    TukTuk.Modal.hide()

__Controller.Source = new SourceCtrl "section"
