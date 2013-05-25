class __View.GraphLine extends Monocle.View

  container: ""

  template: """
    <div data-graph="line">
    </div>
  """

  constructor: ->
    super
    @html @model
