window.Dagora = do ->

  SERVICE   = " http://api.dagora.es/v1/"

  api = (type, method, parameters = {}) ->
    promise = new Hope.Promise()

    do TukTuk.Modal.loading
    $.ajax
      url: SERVICE + method
      type: type
      data: parameters
      dataType: 'json'
      success: (response) =>
        do TukTuk.Modal.hide
        setTimeout ->
          promise.done null, response
        , 300
      error: (xhr, type, request) =>
        do TukTuk.Modal.hide
        setTimeout ->
          promise.done xhr, null
        , 300
    promise


  api: api
