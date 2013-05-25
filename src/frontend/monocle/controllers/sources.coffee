class SourcesCtrl extends Monocle.Controller

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

  fetch: (value) ->
    console.log "fetch", value
    @results.html ""
    Dagora.api("GET", "sources.json", s : value).then (error, sources) =>
      if sources?
        __Model.Source.create source for source in sources
      else
        @mock()
        # console.error "ERROR: ", error

  mock: ->
    __Model.Source.create id: "1", title: "t1", link: "u1", created: new Date(), updated: new Date()
    __Model.Source.create id: "2", title: "t2", link: "u2", created: new Date(), updated: new Date()
    __Model.Source.create id: "3", title: "t3", link: "u3", created: new Date(), updated: new Date()
    __Model.Source.create id: "4", title: "t4", link: "u4", created: new Date(), updated: new Date()

__Controller.Sources = new SourcesCtrl "section"
