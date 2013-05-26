class __View.GraphBar extends Monocle.View

  instance = undefined

  container: "section > article #bar"

  template: """
    <div data-graph="bar">
      <h4 class="text bold color theme uppercase">PROGRESION EN {{unit}}</h4>
      <div class="graph"></div>
    </div>
  """

  constructor: ->
    super
    @html @model

    #Parse Data
    data = [["Año", @model.unit]]
    data.push([stat.date, stat.value]) for stat in @model.data
    data = google.visualization.arrayToDataTable(data)

    options =
      animation: duration: 400
      areaOpacity: 0.1
      backgroundColor: "#ecf0f1"
      chartArea: width:"100%", top: 20
      colors: ["#bdc3c7"]
      fontName: "Oswald"
      fontSize: 12
      legend: position: "none"
      pointSize: 16
      hAxis: textStyle: color: "#999"
      vAxis: minValue: 0, gridlines: {color: "#fff", count: 5}, textStyle: color: "#999"
      height: 232


    @instance = new google.visualization.ColumnChart @el.find(".graph").get(0)
    @instance.draw data, options
