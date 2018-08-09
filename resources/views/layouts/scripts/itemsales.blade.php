 <script>   
    $(document).ready(function() {
        $('#franchise').change(function(){
            var franchise=$('#franchise').val();
            if(franchise!=''){                
                $.ajax({
                    url: "filterByFranchise",
                    cache: false,               
                    data: { franchise: franchise},
                    success: function(val){  
                        $("#cat tr").remove();
                        console.log(val);
                        $.each(val, function(category,data){ 
                            $('#cat').append('<tr><td>'+category+'</td><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td></tr>');
                        });
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }
        });
        $('#filterby').change(function(){
            var filterby=$('#filterby').val();
            var franchise=$('#franchise').val();
            if(filterby!='' && franchise!=''){                
                $.ajax({
                    type: "get",
                    url: "filterCategoryRecord",
                    cache: false,               
                    data: { filterby: filterby, franchise : franchise},
                    success: function(val){ 
                        $("#cat tr").remove();                         
                         $.each(val, function(category,data){ 
                            $('#cat').append('<tr><td>'+category+'</td><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td></tr>');
                        });
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }else{
                alert("Please select required fields");
            }
        });
        $('#export').click(function(){                
            var filterby = $('#filterby').val();
            var franchise = $('#franchise').val();
            if(filterby != '' && franchise != ''){  
                var url = '/itemsales/excel/'+franchise+'/'+filterby;
                window.open(url,'_blank');
            }else{
                alert("Please select required fields");
            }
        });
        $('#export_top_sales').click(function(){                
            var filterby_top = $('#filterby_top').val();
            var franchise_top = $('#franchise_top').val();
            if(filterby_top !='' && franchise_top != ''){ 
                var url = '/itemsales/excelTopSales/'+franchise_top+'/'+filterby_top;
                window.open(url,'_blank');
            }else{
                alert("Please select required fields");
            }
        });
        $('#export_low_sales').click(function(){                
            var filterby_low = $('#filterby_low').val();
            var franchise_low = $('#franchise_low').val();
            if(filterby_low != '' && franchise_low != ''){                              
                var url = '/itemsales/excelLowSales/'+franchise_low+'/'+filterby_low;
                window.open(url,'_blank');
            }else{
                alert("Please select required fields");
            }
        });
        $('#franchise_low').change(function(){
            var franchise_low = $('#franchise_low').val();            
            getFranchiseLow(franchise_low);
        });
        function getFranchiseLow(franchise_low){
            if(franchise_low!=''){
                $.ajax({
                    url: "itemsales/filterByFranchiseLow",
                    cache: false,               
                    data: { franchise: franchise_low},
                    success: function(val){                        
                        $("#lowsales tr").remove();
                         $.each(val, function(category,data){ 
                            $('#lowsales').append('<tr><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td><td>'+data[3]+'</td><td>'+data[4]+'</td></tr>');
                        });                     
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }
        }
        $('#filterby_low').change(function(){
            var filterby_low=$('#filterby_low').val();
            var franchise_low=$('#franchise_low').val();
            if(filterby_low != '' && franchise_low != ''){                
                $.ajax({
                    type: "get",
                    url: "itemsales/filterRecordByLowRange",
                    cache: false,               
                    data: { filterby: filterby_low, franchise : franchise_low},
                    success: function(val){ 
                        console.log(val);
                         $("#lowsales tr").remove();  
                         $.each(val, function(category,data){ 
                            $('#lowsales').append('<tr><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td><td>'+data[3]+'</td><td>'+data[4]+'</td></tr>');
                        });
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }else{
                getFranchiseLow(franchise_low);                
            }
        });
        $('#filterby_top').change(function(){
            var filterby_top=$('#filterby_top').val();
            var franchise_top=$('#franchise_top').val();
            if(filterby_top!='' && franchise_top!=''){                
                $.ajax({
                    type: "get",
                    url: "itemsales/filterRecordByTopRange",
                    cache: false,               
                    data: { filterby: filterby_top, franchise : franchise_top},
                    success: function(val){                         
                         $("#topsales tr").remove();  
                         $.each(val, function(category,data){ 
                            $('#topsales').append('<tr><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td><td>'+data[3]+'</td><td>'+data[4]+'</td></tr>');
                        });
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }else{
                alert("Please select required fields");
            }
        });
        $('#franchise_top').change(function(){            
            var franchise_top=$('#franchise_top').val();            
            if(franchise_top!=''){     
                $.ajax({
                    url: "itemsales/filterByFranchiseTop",
                    cache: false,               
                    data: { franchise: franchise_top},
                    success: function(val){  
                        console.log(val);
                        $("#topsales tr").remove();
                         $.each(val, function(category,data){ 
                            $('#topsales').append('<tr><td>'+data[0]+'</td><td>'+data[1]+'</td><td>'+data[2]+'</td><td>'+data[3]+'</td><td>'+data[4]+'</td></tr>');
                        });                     
                    }, error: function(){                      
                        console.log('Error while request..');
                    }
                });
            }
        });
    });
</script>