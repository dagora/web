class __View.SourceOverview extends Monocle.View

  instance = undefined

  container: "section > article #overview"

  template: """
    <h4 class="text bold uppercase">{{title}}</h4>
    <ul class="margin" data-tuktuk="totals"   id="overview">
        <li>
            <span class="icon book"></span>
            <strong>{{data.length}}</strong>
            <small>registros</small></li>
        <li>
            <strong>{{unit}}</strong>
            <small>unidad</small>
        </li>
        <li>
            <span class="icon dashboard"></span>
            <strong>{{progresion}}%</strong>
            <small>crecimiento</small>
        </li>
    </ul>
    """

  constructor: ->
    super
    progresion = @model.data[@model.data.length - 1].value / @model.data[0].value
    @model.progresion = parseInt(progresion * 100)
    @html @model
