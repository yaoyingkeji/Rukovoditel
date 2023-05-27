class geliossoft_objects
{
    constructor(myMap,field_id,refresh_interval, map_type)
    {
        this.myMap = myMap
        this.geliosObj = new Map()
        this.geliosObjPopup = new Map()
        this.data_url = url_for('ext/map_reports/public','action=getGeliosObjects')        
        this.field_id = field_id   
        this.map_type = map_type
        
        this.error_popup = false
        
        this.init()
        
        setInterval(data=>{
            this.init()
        },refresh_interval*1000)
    }
    
    init()
    {                                
        $.ajax({url:this.data_url,type:'POST',data:{field_id:this.field_id}}).done(data=>{
            
            if(data.length==0) return false
                        
            data = JSON.parse(data)
            
            //check errors
            if(!this.check(data))
            {
                return false;
            }
            
            //console.log(this.map_type)
            
            switch(this.map_type)
            {
                case 'yandex':
                    this.apply_yandex(data)
                    break;
                case 'google':
                    this.apply_google(data)
                    break;
            }            
            
            this.apply_sidebar(data)
        })
    }
    
    check(data)
    {
        if(typeof data.error !== 'undefined')
        {
            if(!this.error_popup)
            {
                alert('GeliosSoft Error: '+data.data_error)
                
                this.error_popup = true
            }
            else
            {
                console.log(data)
            }     
            
            return false;
        }
        else
        {
            return true;
        }
        
    }
    
    apply_sidebar(data)
    {
        if($('.map-sidebar-list').legnth==0 || $('.map-sidebar-list').hasClass('geliossoft_objects'))
        {
            return false;
        }
        else
        {
            $('.map-sidebar-list').addClass('geliossoft_objects')
            
            $("#geliossoft_objects_heading").html(i18n['TEXT_CARS']+' ('+ data.length +')')
        }
        
        for(const obj of data)
        {
            if(typeof obj.lmsg.lat === 'undefined')
            {
                continue 
            }
            
            $('.map-sidebar-geliossoft-objects').append('<a href="javascript: ymap_center('+obj.lmsg.lat+','+obj.lmsg.lon+')" class="list-group-item map-sidebar-item map-sidebar-car-icon" style="background-image: url(\''+obj.unit_icon+'\')">' + obj.name + '</a>')
        }
        
    }
    
    apply_google(data)
    {
        var marker = []
        
        for(const obj of data)
        {
            //console.log(obj)
            
            //console.log(obj.id)
            if(!this.geliosObj.has(obj.id))
            {                                
                                
                if(typeof obj.lmsg.lat === 'undefined')
                {
                    continue 
                }
                
                var options = {
                    map: this.myMap,
                    position: new google.maps.LatLng(obj.lmsg.lat,obj.lmsg.lon),
                    title:"Hello World!",
                    label: obj.name
                }
                
                if(obj.unit_icon && obj.unit_icon.length>0)
                {
                    options.icon = obj.unit_icon                    
                }
                
                marker[obj.id] = new google.maps.Marker(options);
                
                this.geliosObj.set(obj.id,marker[obj.id])
                
                this.geliosObjPopup.set(obj.id,'<h4>'+obj.name+'</h4>'+this.popup(obj)+'<h5>'+obj.phone + ' ' + obj.phone2+'</h5>')
                
                var infowindow = new google.maps.InfoWindow();
                
                google.maps.event.addListener(marker[obj.id], "click", () => {
                    infowindow.close()//hide the infowindow
                    infowindow.setContent(this.geliosObjPopup.get(obj.id))
                    infowindow.open(this.myMap,marker[obj.id])
                });
                               
            }
            else
            {
                //myPlacemark = this.geliosObj.get(obj.id)
                
                //var latlng = new google.maps.LatLng(40.748774, -73.985763);
                this.geliosObj.get(obj.id).setPosition(new google.maps.LatLng(obj.lmsg.lat,obj.lmsg.lon))
                
                this.geliosObjPopup.set(obj.id,'<h4>'+obj.name+'</h4>'+this.popup(obj)+'<h5>'+obj.phone + ' ' + obj.phone2+'</h5>')
                
                /*
                myPlacemark.properties.set({
                    balloonContentBody: this.popup(obj)
                })
                
                myPlacemark.geometry.setCoordinates([obj.lmsg.lat, obj.lmsg.lon]);*/
            }
            
        }
    }
    
    apply_yandex(data)
    {
                
        for(const obj of data)
        {
            //console.log(obj)
            
            //console.log(obj.id)
            if(!this.geliosObj.has(obj.id))
            {                                
                var options = {
                    preset:"islands#circleDotIcon"
                }
                
                if(obj.unit_icon && obj.unit_icon.length>0)
                {
                    options.iconLayout = 'default#image'
                    options.iconImageHref = obj.unit_icon
                }
                
                if(typeof obj.lmsg.lat === 'undefined')
                {
                    continue 
                }
                                                
                var myPlacemark = new ymaps.Placemark([obj.lmsg.lat, obj.lmsg.lon],{
                                iconCaption: obj.name,
                                hintContent: obj.name,
                                balloonContentHeader: obj.name, 
                                balloonContentBody: this.popup(obj),
                                balloonContentFooter: obj.phone + ' ' + obj.phone2
                            },options)
                            
                this.geliosObj.set(obj.id,myPlacemark)
                               
                this.myMap.geoObjects.add(myPlacemark);                                  
            }
            else
            {
                myPlacemark = this.geliosObj.get(obj.id)
                
                myPlacemark.properties.set({
                    balloonContentBody: this.popup(obj)
                })
                
                myPlacemark.geometry.setCoordinates([obj.lmsg.lat, obj.lmsg.lon]);
            }
            
        }
    }
    
    popup(obj)
    {
        let html = ''
       
        if(typeof obj.lmsg.time !== 'undefined')
        {
            let time = new Date(obj.lmsg.time*1000).toLocaleString("ru-RU")
            html += '<div class="geliossoft-obj-time">' + i18n['TEXT_LAST_MESSAGE'] +': '+ time + ' (' + this.time_difference(obj.lmsg.time*1000)  + ')</div>'                        
        }
        
        if(obj.lmsg.address.length>0)
        {
            html += '<div class="geliossoft-obj-address">' + obj.lmsg.address + '</div>' 
        }
       
        obj.lmsg.sensors.forEach(value=>{            
            if(value.textValue.length>0 || value.value>0)
            {
                html += '<div class="geliossoft-obj-sensor"><b>' + value.name + '</b>: ' + (value.textValue.length>0 ? value.textValue:value.value) + '</div>'
            }
        })
        
        html += '<div class="geliossoft-obj-sensor"><b>' + i18n['TEXT_SPEED'] + '</b>: ' + obj.lmsg.speed + ' ' + i18n['TEXT_SPEED_KPH'] +  '</div>'
       
       return html
    }
    
    time_difference(start_date, end_date)
    {
        let obj = get_time_difference(start_date, end_date)

        let text = i18n['TEXT_DATEPICKER_DAYS_HOURS_MINUTES_SHORT'].split(',')

        let str = ''

        if(obj.days>0)
        {
           str+= obj.days + text[0] + ' '
        }

        if(obj.hours>0)
        {
           str+= obj.hours + text[1] + ' '
        }

        if(obj.minutes>0)
        {
           str+= obj.minutes + text[2] + ' '
        }

        if(obj.seconds>0)
        {
           str+= obj.seconds + text[3] + ' '
        }

        if(str.length>0)
        {
            str += ' ' + i18n['TEXT_AGO'] 
        }

        return str;
    }
    
}


