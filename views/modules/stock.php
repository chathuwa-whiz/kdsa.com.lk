<div class="content-wrapper" style="background-color: #f8f9fa; padding: 20px;">
    <div class="container" style="margin-top: 50px;">
        <div class="row">
            <div class="col-md-12">
                <!-- <h2 class="text-center" style="margin-bottom: 30px; color: #343a40;">Stock Table</h2> -->

                <!-- Search input -->
                <input class="form-control" id="searchInput" type="text" placeholder="Search for product names..." style="margin-bottom: 20px; border-radius: 10px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">

                <!-- Date filter inputs -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="startDate" placeholder="Start Date" style="border-radius: 10px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="endDate" placeholder="End Date" style="border-radius: 10px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                    </div>
                </div>

                <div class="panel-group" id="stockAccordion">
                    <?php
                        $item = null;
                        $value = null;

                        // Fetch the stock data
                        $stocks = ControllerUsers::ctrShowStock($item, $value);

                        // Group data by product name and sort by date
                        $groupedStocks = [];
                        foreach ($stocks as $stock) {
                            $groupedStocks[$stock['productName']][] = $stock;
                        }

                        // Sort each product group by date (newest first)
                        foreach ($groupedStocks as &$stockItems) {
                            usort($stockItems, function($a, $b) {
                                return strtotime($b['date']) - strtotime($a['date']);
                            });
                        }
                        unset($stockItems);

                        // Display the grouped and sorted data
                        foreach ($groupedStocks as $productName => $stockItems) {
                            $collapseId = 'collapse' . md5($productName);
                            echo '
                            <div class="panel panel-default" style="border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 15px;">
                                <div class="panel-heading" id="heading' . $collapseId . '" style="background-color: #007bff; border-radius: 10px; padding: 15px;">
                                    <h4 class="panel-title" style="margin: 0;">
                                        <a class="collapsed" data-toggle="collapse" data-parent="#stockAccordion" href="#' . $collapseId . '" style="display: block; width: 100%; text-decoration: none; color: #fff;">
                                            ' . $productName . ' <span class="caret"></span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="' . $collapseId . '" class="panel-collapse collapse">
                                    <div class="panel-body" style="background-color: #fff; border-radius: 10px; padding: 15px;">
                                        <table class="table table-bordered table-hover table-striped" style="border-radius: 10px;">
                                            <thead>
                                                <tr class="info" style="background-color: #f8f9fa; color: #343a40;">
                                                    <th>Product Name</th>
                                                    <th>Stock Count</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                            foreach ($stockItems as $stockItem) {
                                echo '
                                                <tr data-date="' . $stockItem["date"] . '">
                                                    <td>' . $stockItem["productName"] . '</td>
                                                    <td>' . $stockItem["stockCount"] . '</td>
                                                    <td>' . $stockItem["date"] . '</td>
                                                </tr>';
                            }
                            echo '
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Search filter
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".panel").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Date filter
        $("#startDate, #endDate").on("change", function() {
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            $(".panel").each(function() {
                var panel = $(this);
                var showPanel = false;

                panel.find("tr[data-date]").each(function() {
                    var date = $(this).data("date");
                    if ((!startDate || date >= startDate) && (!endDate || date <= endDate)) {
                        showPanel = true;
                        return false; // No need to check further, show the panel
                    }
                });

                panel.toggle(showPanel);
            });
        });
    });
</script>
