class __View.GraphPie extends Monocle.View

  instance = undefined

  container: "section > article #pie"

  template: """
    <li class="margin-top" data-graph="pie">
      <h4 class="text bold color theme uppercase">{{title}}</h4>
      <div class="graph"></div>
    </li>
  """

  constructor: ->
    super
    @append @model
    options =
      colors: ["3498db", "#ddd"]
      legend: position: 'none'
      fontSize: 18
      fontName: "Oswald"
      chartArea: width:"75%", height:"75%", top: 0
      tooltip: trigger: "focus", showColorCode: true

    data = google.visualization.arrayToDataTable([
      ['Value', 'Value'],
      [@model.name, @model.percent],
      ['Available',  100- @model.percent],
    ])
    @instance = new google.visualization.PieChart @el.find(".graph").get(0)
    @instance.draw data, options
