class QueryCtrl extends Monocle.Controller

  constructor: ->
    super

$ ->
  __Controller.Query = new QueryCtrl "body"
