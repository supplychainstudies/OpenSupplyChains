/* Copyright (C) Sourcemap 2011
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. */

function SrcClient() {
	
	this.API_VERSION = '1.0';
	this.API_ENDPOINT = 'http://www.sourcemap.com/services/';
	this.apikey = '';
	this.apisecret = '';
	
	this.init = function(key, secret) {
		this.apikey = key;
		this.apisecret = secret;
	}

	this.available = function(cbname) {
		this._get('', cbname);
	}

	this.get_supplychain = function(id, cbname) {
		this._get('supplychains/'+id, cbname);
	}

	this.get_supplychains = function(args, cbname) {
		if(args[0] && typeof(args[0]) == 'number') {
			var l = args[0];
			if(args[1]) { var o = args[1]; } else { var o = 0; }
			this._get('supplychains/', cbname, '?l='+l+'&o='+o+'&');		
		} else if(typeof(args[0]) == 'string') {
			this._get('search/', cbname, '?q='+args[0]+'&');		
		} else { throw 'Invalid Arguments'; }
	}

	this._get = function(service, cbname, get) {
		if(typeof(get) == 'undefined') { get = '?'; }
		var script = document.createElement('script');
		script.setAttribute('src', this.API_ENDPOINT+service+get+'f=jsonp&callback='+cbname);
		document.getElementsByTagName('head')[0].appendChild(script);
	}
}