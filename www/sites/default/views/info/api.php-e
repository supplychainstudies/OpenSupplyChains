<div class="grid container_16">

    <div class="documentation">
        <h1>Sourcemap Developer Tools</h1>
        <div class="container_16">
            <div class="grid_4">
                <div class="toc">
                <ol class="classic-list">
                    <li><a href="/info/api#intro">Introduction</a></li>
                    <li><a href="/info/api#api">API</a>
                        <ol>
                            <li><a href="/info/api#api-overview">Overview</a></li>
                            <li><a href="/info/api#api-authentication">Authentication</a></li>
                            <li><a href="/info/api#api-supplychains">Supplychains</a>
                                <ol>
                                    <li>Fetching Supplychains</li>
                                    <li>Creating/Updating Supplychains</li>
                                </ol>
                            </li>
                            <li><a href="/info/api#api-search">Search</a></li>
                            <li>QR Codes</li>
                            <li>Static Maps</li>
                        </ol>
                    </li>
                    <li><a href="/info/api#code">Code</a></li>
                </ol>
                </div>
            </div>

            <div class="grid_12">
                <div class="section" id="intro">
                    <h2>Introduction</h2>
                    <p>
                        Sourcemap is committed to providing tools to our users to make it as easy as possible to
                        build interesting and useful applications on top of and alongside our essential offerings.
                        Learn more about our web services <a href="/info/api/#api">API</a> and source <a href="/info/api/#code">
                        code</a> repositories below.
                    </p>
                </div>

                <div class="section" id="api">
                    <h2>API</h2>
                    <div class="subsection" id="api-overview">
                        <h3>Overview</h3>
                        <p>
                            The Sourcemap API provides web services developers can use to interact with Sourcemap data, users, maps, and tools.
                            Where possible, the services try to adhere to <a href="">REST</a> principles. In general, services are accessed at
                            pathnames beginning with <span class="codeface">services/</span>. Data is accessed with or without authentication
                            using the HTTP methods <span class="codeface">GET</span>, <span class="codeface">PUT</span>, <span class="codeface">
                            POST</span>, and <span class="codeface">DELETE</span>.
                        </p>
                        <p>
                            A very minimalistic "discovery" service exists at the base services url <span class="codeface">/services/</span>.
                        </p>
                        <div class="code-sample bash">
curl -is http://sourcemap.com/services/
                        </div>
                        <p>
                            The above command will yield JSON (other <a href="#api-serialization">serialization</a> formats are available) like the
                            following.
                        </p>
                        <div class="code-sample json">
HTTP/1.1 200 OK
Date: Mon, 1 Jan 2099 18:50:37 GMT
Content-Length: 50
Content-Type: application/json

{"services":["search","supplychains"],"you":false}
                        </div>
                    </div><!-- subsection api-overview -->

                    <div class="subsection" id="api-authentication">
                        <h3>Authentication</h3>
                        <p>Authentication for the Sourcemap API centers around tokens generated using an API key-secret pair.
                            The key is generated randomly and assigned upon <a href="mailto: api@sourcemap.com">request</a>.
                            Since we don't currently support any kind of three-party authentication, you can only use the API 
                            to get public data or private data that belongs to you using key-secret authentication.
                        </p>
                        <p>To use your API key, you must construct a few special HTTP headers for every request you'd like to
                            authenticate. Every authenticated API request requires the <span class="codeface">Date</span> header.
                            The date is expected to be in <a href="">RFC2822</a> format. This date should matche the date and time
                            on the Sourcemap servers to within 30 seconds. The API key should be included in the special HTTP header
                            <span class="codeface">X-Sourcemap-API-Key</span>.
                        </p>
                        <p>
                            The final header required for authentication with the Sourcemap API is the <span class="codeface">
                            X-Sourcemap-API-Token</span> header. It is constructed by concatenating the values of the 
                            <span class="codeface">Date</span> header, the API key, and the API secret then hashing them using
                            the <span class="codeface">md5</span> algorithm, widely available in the most popular scripting
                            languages. This could be done in PHP as follows, assuming <span class="codeface">$date</span>, 
                            <span class="codeface">$apikey</span>, and <span class="codeface">$apisecret</span> are properly set:
                        </p>
                        <div class="code-sample php">
$apitoken = md5(sprintf('%s-%s-%s', $date, $apikey, $apisecret));
                        </div>
                    </div><!-- end subsection api-overview -->

                    <div class="subsection" id="api-supplychains">
                        <h3>Supplychains</h3>
                        <div class="subsubsection" id="api-supplychains-get">
                            <h4>&raquo; Fetching Supplychains</h4>
                            <p>The supplychains service provides an endpoint for fetching lists of public supplychains. You can
                                set the limit and offset values using the query string parameters <span class="codeface">l</span>
                                and <span class="codeface">o</span>, respectively.
                            </p>
                            <div class="code-sample bash">
curl -is 'http://sourcemap.com/services/supplychains/?l=10&amp;o=25'
                            </div>
                            <div class="code-sample json">
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

                            </div>
                            <p>
                                The <span class="codeface">supplychains</span> endpoint isn't a good method for finding
                                supplychains based on their attributes. This is better accomplished using our <a href="">search</a>
                                services.
                            </p>
                            <p> 
                                Individual supplychains may be accessed using paths of the form <span class="codeface">services/supplychains/[id]</span>
                                where <span class="codeface">[id]</span> is a supplychain ID.
                            </p>
                            <div class="code-sample bash">
    curl -is 'http://sourcemap.com/services/supplychains/12345'
                            </div>
                            <p>Would return the following:</p>
                            <div class="code-sample json">
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
                            </div>
                        </div><!-- end subsubsection supplychain-get -->
                    </div><!-- end subsection supplychains -->

                    <div class="subsection" id="search">
                        <h3>Search</h3>
                        <p>The <span class="codeface">simple</span> search feature allows
                            API consumers to search supplychains by keyword and/or category.
                        </p>
                        <div class="code-sample bash">
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
                        </div>
                    </div>
                </div><!-- end section api -->




            </div>
        </div>
    </div>
</div>
