class SearchCtrl extends Monocle.Controller

  elements:
    "input"                         : "txtSearch"
    "[data-context=search] > table" : "results"

  events:
    "click header button"           : "search"
    "keypress input"                : "onSearch"

  constructor: ->
    super
    __Model.Source.bind "create", @bindSourceCreated

  # BINDS
  bindSourceCreated: (instance) -> new __View.SourceListItem model: instance

  # EVENTS
  onSearch: (event) ->
    __Controller.Source.reset()
    do @search if event.keyCode is 13

  search: ->
    if @txtSearch.val()
      __Controller.Source.reset()
      @url @txtSearch.val()

  # INSTANCE
  fetch: (value) ->
    @results.html ""
    Dagora.api("GET", "sources.json", s : value).then (error, response) =>
      if response?
        __Model.Source.create source for source in response.data.resultsList
      else
        alert "Algo ha ido mal"

__Controller.Search = new SearchCtrl "section"
