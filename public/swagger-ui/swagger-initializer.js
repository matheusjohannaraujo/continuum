window.onload = function() {
  //<editor-fold desc="Changeable Configuration Block">

  // "http://localhost/continuum/swagger-php-open-api"
  let base_url = (window.location.origin + window.location.pathname).replace("public/swagger-ui/", "") + "swagger-php-open-api";

  // the following lines will be replaced by docker/configurator, when it runs in a docker-container
  window.ui = SwaggerUIBundle({
    //url: "https://petstore.swagger.io/v2/swagger.json",
    url: base_url,
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  });

  //</editor-fold>
};
