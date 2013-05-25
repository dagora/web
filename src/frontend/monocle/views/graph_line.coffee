class __View.GraphLine extends Monocle.View

  instance = undefined

  container: "section#results > article #line"

  template: """
    <div data-graph="line">
      <h3 class="text bold">{{title}}</h3>
      <div class="graph"></div>
    </div>
  """

  constructor: ->
    super
    @html @model

    options =
      animation: {duration: 1000, easing: "linear"}
      areaOpacity: 0.1
      # chartArea: top: 20, width:"85%", height:"90%"
      colors: ["#602BAB", "#666"]
      fontName: "Lato"
      legend: position: 'none'
      pointSize: 16
      vAxis: gridlines: color: "#ddd", count: 0
      # width: "50%"
      # height: "50%"

    data = google.visualization.arrayToDataTable(@model.data)
    @instance = new google.visualization.ColumnChart @el.find(".graph").get(0)
    @instance.draw data, options
