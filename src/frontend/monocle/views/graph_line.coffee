class __View.GraphLine extends Monocle.View

  instance = undefined

  container: "section#results > article #line"

  template: """
    <div data-graph="line">
      <h4 class="text bold color theme">{{title}} <span class="text book color default italic">({{source}})</span></h4>
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
      # chartArea: top: 20, width:"85%", height:"90%"
      colors: ["#bdc3c7", "#666"]
      fontName: "Oswald"
      legend: position: 'none'
      pointSize: 16
      vAxis: gridlines: color: "#ddd", count: 0
      # width: "50%"
      # height: "50%"

    data = google.visualization.arrayToDataTable(@model.data)
    @instance = new google.visualization.ColumnChart @el.find(".graph").get(0)
    @instance.draw data, options
