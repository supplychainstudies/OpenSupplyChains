<div class="grid container_16">

    <div class="documentation">
        <h1>Sourcemap Developer Tools</h1>
        <div class="container_16">
            <div class="grid_6">
                <div class="toc">
                <ol class="classic-list">
                    <li><a href="api#intro">Introduction</a></li>
                    <li><a href="api#api">API</a>
                        <ol>
                            <li>Overview</li>
                            <li>Authentication</li>
                            <li>Supplychains</li>
                            <li>Search</li>
                            <li>QR Codes</li>
                            <li>Static Maps</li>
                        </ol>
                    </li>
                    <li><a href="api#code">Code</a></li>
                </ol>
                </div>
            </div>

            <div class="grid_10">
                <div class="section" id="intro">
                    <h2>Introduction</h2>
                    <p>
                        Sourcemap is committed to providing tools to our users to make it as easy as possible to
                        build interesting and useful applications on top of and alongside our essential offerings.
                        Learn more about our web services <a href="api/#api">API</a> and source <a href="api/#code">
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

                    <div class="subsection" id="api-supplychain">
                        <h3>Supplychains</h3>
                        <p>The supplychains service provides an endpoint for fetching lists of public supplychains. You can
                            set the limit and offset values using the query string parameters <span class="codeface">l</span>
                            and <span class="codeface">o</span>, respectively.
                        </p>
                        <div class="code-sample bash">
curl -is http://sourcemap.com/services/supplychain/?l=25&amp;o=25
                        </div>
                    </div>
                </div><!-- end section api -->




            </div>
        </div>
    </div>
</div>
