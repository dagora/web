class __View.GraphBar extends Monocle.View

  instance = undefined

  container: "section > article #bar"

  template: """
    <div data-graph="bar">
      <h4 class="text bold color theme uppercase">{{title}} <span class="text book color default italic">({{source}})</span></h4>
      <div class="graph"></div>
    </div>
  """

  constructor: ->
    super
    @html @model

    options =
      animation: {duration: 1000, easing: "linear"}
      areaOpacity: 0.1
      backgroundColor: "#ecf0f1"
      chartArea: width:"100%"
      colors: ["#bdc3c7"]
      fontName: "Oswald"
      fontSize: 12
      legend: position: 'none'
      pointSize: 16
      hAxis: baselineColor: "#f00", textStyle: color: "#aaa"
      vAxis: gridlines: color: "#ecf0f1", count: 0
      width: "100%"
      height: 292

    data = google.visualization.arrayToDataTable(@model.data)
    @instance = new google.visualization.ColumnChart @el.find(".graph").get(0)
    @instance.draw data, options
