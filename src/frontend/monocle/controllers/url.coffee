class UrlCtrl extends Monocle.Controller

  elements:
    "[data-context]" : "context"

  constructor: ->
    super
    @routes
      ":context/:id"  : @source
      ":context"      : @search
    Monocle.Route.listen()
    # Force URL
    @search null unless window.location.hash

  search: (parameters) ->
    @_context "search"
    if parameters then __Controller.Search.fetch parameters.context

  source: (parameters) ->
    @_context "source"
    if parameters then __Controller.Source.fetch parameters.id


  _context: (value) ->
    @context.hide().siblings("[data-context=#{value}]").show()

$ ->
  __Controller.Url = new UrlCtrl "section"
