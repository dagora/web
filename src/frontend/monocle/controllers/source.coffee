class SourceCtrl extends Monocle.Controller

  elements:
    ".box"          : "boxes"
    "#pie"          : "pies"
    "#updated"      : "updated"

  constructor: ->
    super
    __Model.Stat.bind "create", @bindStatCreated

  # BINDS
  bindStatCreated: (instance) =>
    # RESET
    @pies.html ""
    @updated.html instance.updated

    # OVEWVIEW
    new __View.SourceOverview model: instance
    # BAR
    new __View.GraphBar model: instance
    # PIES
    new __View.GraphPie model: title: "Dato 1", percent: 25
    new __View.GraphPie model: title: "Dato 2", percent: 75
    new __View.GraphPie model: title: "Dato 3", percent: 34
    new __View.GraphPie model: title: "Dato 4", percent: 17

    @boxes.addClass "active"


  # INSTANCE
  fetch: (id) ->
    __Model.Stat.destroyAll()
    Dagora.api("GET", "sources/#{id}.json").then (error, response) =>
      __Model.Stat.create response.data if response?

  reset: ->
    @boxes.removeClass "active"


__Controller.Source = new SourceCtrl "section article[data-context=source]"
