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
        promise.done null, response
      error: (xhr, type, request) =>
        do TukTuk.Modal.hide
        promise.done xhr, null
    promise


  api: api
