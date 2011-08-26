# Sourcemap.com API

The Sourcemap API provides web services developers can use to interact with Sourcemap data, users, maps, and tools. Where possible, the services try to adhere to REST principles. In general, services are accessed at pathnames beginning with `services/`. Data is accessed with or without authentication using the HTTP methods `GET`, `PUT`, `
POST`, and `DELETE`.

A very minimalistic "discovery" service exists at the base services url `/services/`.

	curl -is http://sourcemap.com/services/ 

The above command will yield JSON (other serialization formats are available) like the following. 

	HTTP/1.1 200 OK
	Date: Mon, 1 Jan 2099 18:50:37 GMT
	Content-Length: 50
	Content-Type: application/json

	{"services":["search","supplychains"],"you":false}

### Authentication

Authentication for the Sourcemap API centers around tokens generated using an API key-secret pair. The key is generated randomly and assigned upon [request][1]. Since we don't currently support any kind of three-party authentication, you can only use the API to get public data or private data that belongs to you using key-secret authentication. 

To use your API key, you must construct a few special HTTP headers for every request you'd like to authenticate. Every authenticated API request requires the `Date` header. The date is expected to be in RFC2822 format. This date should matche the date and time on the Sourcemap servers to within 30 seconds. The API key should be included in the special HTTP header `X-Sourcemap-API-Key`. 

The final header required for authentication with the Sourcemap API is the ` X-Sourcemap-API-Token` header. It is constructed by concatenating the values of the `Date` header, the API key, and the API secret then hashing them using the `md5` algorithm, widely available in the most popular scripting languages. This could be done in PHP as follows, assuming `$date`, `$apikey`, and `$apisecret` are properly set: 

	$apitoken = md5(sprintf('%s-%s-%s', $date, $apikey, $apisecret));
 
### Supplychains

#### Fetching Supplychains

The supplychains service provides an endpoint for fetching lists of public supplychains. You can set the limit and offset values using the query string parameters `l` and `o`, respectively. 

	curl -is 'http://sourcemap.com/services/supplychains/?l=10&o=25'

	{
	    "supplychains":[
	        {"id":27,"created":1300813400},
	        {"id":28,"created":1300908605},
	        {"id":29,"created":1300912711},
	        {"id":30,"created":1300913070},
	        {"id":31,"created":1302014532},
	        {"id":32,"created":1302014685},
	        {"id":33,"created":1302014775},
	        {"id":34,"created":1302889435},
	        {"id":35,"created":1302890553},
	        {"id":36,"created":1302890575},
	    ],
	    "total":128,
	    "limit":10,
	    "offset":25
	}
 
The `supplychains` endpoint isn't a good method for finding supplychains based on their attributes. This is better accomplished using our search services.

Individual supplychains may be accessed using paths of the form `services/supplychains/[id]` where `[id]` is a supplychain ID.

	curl -is 'http://sourcemap.com/services/supplychains/12345'
 
Would return the following:

	HTTP/1.1 200 OK
	Date: Mon, 1 Jan 2099 19:59:55 GMT
	Content-Length: 1515
	Content-Type: application/json

	{
	    "supplychain":{
	        "category":null,"created":1298652652,
	        "flags":32,"id":1,"modified":1306106036,
	        "other_perms":1,"usergroup_id":null,
	        "usergroup_perms":0,"user_id":234,
	        "owner":{
	            "id":444,"name":"somefakeuserguy",
	            "avatar":"http:\/\/www.gravatar.com\/avatar\/..."
	        },
	        "taxonomy":null,
	        "attributes":{},
	        "stops":[
	            {
	                "local_stop_id":5,"id":5,"geometry":
	                    "POINT(-9349165.430522 4044184.943345)",
	                "attributes":{
	                    "name":"Facility #5"
	                }
	            },{
	                "local_stop_id":4,"id":4,"geometry":
	                    "POINT(-10634992.255936 3485526.892738)",
	                "attributes":{
	                    "name":"Facility #4"
	                }
	            },{
	                "local_stop_id":3,"id":3,"geometry":
	                    "POINT(-12489606.041822 3954200.282625)",
	                "attributes":{
	                    "name":"Facility #3"
	                }
	            },{
	                "local_stop_id":2,"id":2,"geometry":
	                    "POINT(-7929147.678904 5239202.289146)",
	                "attributes":{
	                    "name":"Facility #2"
	                }
	            },{
	                "local_stop_id":1,"id":1,"geometry":
	                    "POINT(-10804007.180522 3869332.593955)",
	                "attributes":{
	                    "name":"Facility #1"
	                }
	            }
	        ],
	        "hops":[
	            {
	                "from_stop_id":3,"to_stop_id":1,
	                "geometry":
	                    "MULTILINESTRING((-12489606.041822 3954200.282625,
	                    -10804007.180522 3869332.593955))",
	                "attributes":{}
	            },{
	                "from_stop_id":3,"to_stop_id":2,
	                "geometry":
	                    "MULTILINESTRING((-12489606.041822 3954200.282625,
	                    -7929147.678904 5239202.289146))",
	                "attributes":{}
	            },{
	                "from_stop_id":3,"to_stop_id":4,
	                "geometry":
	                    "MULTILINESTRING((-12489606.041822 3954200.282625,
	                    -10634992.255936 3485526.892738))",
	                "attributes":{}
	            },{
	                "from_stop_id":3,"to_stop_id":5,
	                "geometry":
	                    "MULTILINESTRING((-12489606.041822 3954200.282625,
	                    -9349165.430522 4044184.943345))",
	                "attributes":{}
	            }
	        ]
	    },"editable":false
	}
 
### Search

The `simple` search feature allows API consumers to search supplychains by keyword and/or category.

	HTTP/1.1 200 OK
	Date: Mon, 1 Jan 2099 20:15:11 GMT
	Content-Length: 432
	Content-Type: application/json

	{
	    "search_type":"simple","offset":0,"limit":25,
	    "hits_tot":1,"hits_ret":1,
	    "parameters":{
	        "q":"texas"
	    },
	    "results":[
	        {
	            "category":null,"created":1300738955,"flags":0,
	            "id":26,"modified":1300738995,
	            "other_perms":1,"usergroup_id":null,
	            "usergroup_perms":0,"user_id":1,
	            "attributes":{
	                "name":"BSR2010"
	            },
	            "owner":{
	                "created":1298652655,"id":1,"last_login":1307583315,
	                "logins":40,"username":"administrator",
	                "name":"administrator"
	            }
	        }
	    ],
	    "cache_hit":true}

[1]: mailto:api@sourcemap.com