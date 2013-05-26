class __View.SourceOverview extends Monocle.View

  instance = undefined

  container: "section > article #overview"

  template: """
    <h4 class="text bold uppercase">{{title}}</h4>
    <ul class="margin" data-tuktuk="totals" id="overview">
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
            <strong>{{percent}}%</strong>
            <small>progresion</small>
        </li>
    </ul>
    """

  constructor: ->
    super
    @model.percent = parseInt((@model.data[@model.data.length - 1].value * 100) / @model.data[0].value)

    @html @model
