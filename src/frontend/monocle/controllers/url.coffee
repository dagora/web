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
    do @search unless window.location.hash

  search: (parameters) ->
    @_context "search"
    if parameters then __Controller.Sources.fetch parameters.context

  source: ->
    @_context "source"
    @

  _context: (value) ->
    @context.hide().siblings("[data-context=#{value}]").show()

$ ->
  __Controller.Url = new UrlCtrl "section"
