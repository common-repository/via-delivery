<div style="width: 100%; height: 240px; position: relative">
    <canvas id="chart_canvas_shipment"></canvas>
</div>

<script>
    (function(){
        const log = (...msg) => {
        console.log(msg.join(" "));
      };

      const logj = (obj) => {
        console.log(JSON.stringify(obj, null, 2));
      };

      function get(name) {
        if (
          (name = new RegExp(
            "[?&]" + encodeURIComponent(name) + "=([^&]*)"
          ).exec(location.search))
        )
          return decodeURIComponent(name[1]);
      }

      var chart,
        loaded = false,
        chartDataCreated = [],
        chartDataDelivered = [],
        chartDataPickedUp = [],
        chartDataUnclailmed = [],
        chartLabels = [];

      function loadData() {
        fetch(
          "https://stat-api.viadelivery.pro/chart/daily-shipments?id=<?= $settings['shop_id'] ?>",
          { method: "GET", headers: {} }
        )
          .then(function (response) {
            return response.json();
          })
          .then(function (jsonData) {
            (chartDataCreated = []),
              (chartDataDelivered = []),
              (chartDataPickedUp = []),
              (chartDataUnclailmed = []),
              (chartLabels = []);
            for (row of jsonData) {
              chartDataCreated.push(Number(row.created));
              chartDataDelivered.push(Number(row.delivered));
              chartDataPickedUp.push(Number(row.picked_up));
              chartDataUnclailmed.push(Number(row.unclaimed));
              chartLabels.push(row.date);
            }
            startDrawing();
          });
      }

      var dictonary = {
        en: {
          "Total shipped": "Total shipped",
          Delivered: "Delivered",
          Issued: "Picked up",
          Unclaimed: "Unclaimed",
        },
        ru: {
          "Total shipped": "Всего отгружено",
          Delivered: "Доставлено",
          Issued: "Выдано",
          Unclaimed: "Не востребовано",
        },
      };

      function t(phrase) {
        var locale = '<?= $locale ?>' || "en";
        return dictonary[locale][phrase] || phrase;
      }

      function drawChart() {
        var ctx = document.getElementById("chart_canvas_shipment").getContext("2d");
        chart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: chartLabels,
            datasets: [
              {
                label: t("Total shipped"),
                backgroundColor: "#A0AEC0",
                borderColor: "#A0AEC0",
                data: chartDataCreated,
              },
              {
                label: t("Delivered"),
                backgroundColor: "#FAF089",
                borderColor: "#FAF089",
                data: chartDataDelivered,
              },
              {
                label: t("Issued"),
                backgroundColor: "#68D391",
                borderColor: "#68D391",
                data: chartDataPickedUp,
              },
              {
                label: t("Unclaimed"),
                backgroundColor: "#F56565",
                borderColor: "#F56565",
                data: chartDataUnclailmed,
              },
            ],
          },
          options: {
            legend: {
              position: "bottom",
            },
            responsive: true,
            maintainAspectRatio: false,
          },
        });
      }
      function startDrawing() {
        // var container = document.getElementById("container");
        // if (container) {
        //   container.style.height = window.innerHeight + "px";
        //   container.style.width = window.innerWidth + "px";

          drawChart();
        // } else {
        //   setTimeout(startDrawing(), 1000);
        // }
      }

      var oldonload = window.onload || function() {};

        window.onload = function() {
            oldonload();
            loadData();
        }
    })();

</script>