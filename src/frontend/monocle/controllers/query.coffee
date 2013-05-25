class QueryCtrl extends Monocle.Controller

  constructor: ->
    super
    console.error "hello world :)"
    # TukTuk.Modal.show "modal"
    # TukTuk.Modal.loading()


    # mock = [
    #   ['Year', 'Sales', 'Expenses'],
    #   ['Apr/2008',  1000,      400],
    #   ['2005',  1170,      460],
    #   ['2006',  660,       1120],
    #   ['2007',  1030,      540],
    # ]

    mock = [
      ['Year', 'Sales'],
      ['Apr/20',  1000],
      ['xxx/XX',  1234],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170],
      ['xxx/XX',  1170]
    ]
    query = __Model.Query.create
      title: "TITULO DE CONSULTA"
      source: "Publico.es"
      unit: "people"
      data: mock

    new __View.GraphLine model: query


$ ->
  __Controller.Query = new QueryCtrl "body"
