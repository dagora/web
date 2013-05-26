class SearchCtrl extends Monocle.Controller

  elements:
    "input"                         : "txtSearch"
    "[data-context=search] > table" : "results"

  events:
    "click button"                  : "search"
    "keypress input"                : "onSearch"

  constructor: ->
    super
    __Model.Source.bind "create", @bindSourceCreated

  # BINDS
  bindSourceCreated: (instance) -> new __View.SourceListItem model: instance

  # EVENTS
  onSearch: (event) ->
    @url @txtSearch.val() if event.keyCode is 13 and @txtSearch.val()?

  # INSTANCE
  fetch: (value) ->
    @results.html ""
    Dagora.api("GET", "sources.json", s : value).then (error, response) =>
      if response?
        __Model.Source.create source for source in response.data.resultsList
      else
        alert "Algo ha ido mal"

__Controller.Search = new SearchCtrl "section"
