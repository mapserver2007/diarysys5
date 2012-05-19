Mixjs.module("Archive", {
    include: Http,
    
    draw: function() {
        var data = {
            data: [
               [1, 30],
               [2, 12],
               [3, 13],
               [4, 10],
               [5, 40],
               [6, 32],
               [7, 25],
               [8, 22],
               [9, 64],
               [10, 10],
               [11, 3],
               [12, 5],
               [13, 19],
               [14, 29],
               [15, 59],
               [16, 9],
               [17, 39],
               [18, 49],
               [19, 20],
               [20, 22],
               [21, 25],
               [22, 10],
               [23, 32],
               [24, 29],
               [25, 45],
               [26, 32],
               [27, 33],
               [28, 9],
               [29, 16],
               [30, 18],
               [31, 32],
               [32, 4],
               [33, 35],
               [34, 22],
               [35, 22],
               [36, 33],
               [37, 22],
               [38, 42],
               [39, 10],
               [40, 2],
               [41, 4],
               [42, 2],
               [43, 6],
               [44, 9],
               [45, 10],
               [46, 22],
               [47, 2],
               [48, 34],
               [49, 15],
               [50, 10],
               [51, 8],
               [52, 11]
            ]
        };
        $.plot($("#graph"), [data], {
            grid: {
                show: true,
                color: "#666",
                borderWidth: 1,
                borderColor: "#ccc"
            },
            bars: {
                show: true,
                lineWidth: 1,
                barWidth: 0.7,
                fillColor: {
                    colors: [
                        {opacity: 0.8},
                        {opacity: 0.5}
                    ]
                },
                align: "left"
            },
            xaxis: {
                show: false
            },
            yaxis: {
                show: false
            },
            hoverable: true,
            clickable: true,
            colors: ["rgb(65, 105, 225)"]
        });
        
        
        $("#graph").bind("barhover", function(e, pos, item) {
            console.log("kita-")
        });
    },
    
    drawGraph: function() {
        
        this.xhr({
            url: "/eclipse/diarysys5/archive_graph",
            args: {type: "get", dataType: "json"},
            successCallback: function(res) {
                console.log(res);
            },
            errorCallback: function(res) {
                alert("error")
            }
            
        })
        
        
        
    },
    
});