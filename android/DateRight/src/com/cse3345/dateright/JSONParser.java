package com.cse3345.dateright;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.List;
import java.util.Map;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.protocol.HTTP;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

public class JSONParser {
	static InputStream is = null;
	static JSONObject jObj = null;
	static String json = "";

	// constructor
	public JSONParser() {
	}

	public JSONObject getJSONFromUrl(String url, Map<String,String> paramss) {
		// Check to see if were checking to see if the user has a favorite
		try {

			DefaultHttpClient httpClient = new DefaultHttpClient();
			HttpPost httpPost = new HttpPost(url);
			if (paramss != null){
				//httpPost.setEntity(new UrlEncodedFormEntity(paramss));
				//entity.setContentType(new BasicHeader(HTTP.CONTENT_TYPE, "application/json"));
				httpPost.setHeader("Content-type", "application/json");
				httpPost.setHeader("Accept", "application/json");
				JSONObject obj = new JSONObject();
				obj.put("email",paramss.get("email").toString());
				obj.put("password",paramss.get("password").toString());
				httpPost.setEntity(new StringEntity(obj.toString(), "UTF-8"));
				//httpPost.setEntity(entity);
			}
			HttpResponse httpResponse = httpClient.execute(httpPost);
			HttpEntity httpEntity = httpResponse.getEntity();
			is = httpEntity.getContent();

		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		boolean emailExists = false;
		try {
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					is, "iso-8859-1"), 8);
			StringBuilder sb = new StringBuilder();
			String line = null;
			while ((line = reader.readLine()) != null) {
				sb.append(line + "\n");
				if (line.equals("100")) {
					emailExists = true;
					break;
				}

			}
			is.close();
			json = sb.toString();

			Log.e("JSON", json);
		} catch (Exception e) {
			Log.e("Buffer Error", "Error converting result " + e.toString());
		}

		// Parse string to JSON object
		try {
			// See if the email already exists and if so create seperate JSON
			// object to return
		 if (emailExists) {
				jObj = new JSONObject();
				jObj.put("emailAlready", true);
			} else
				jObj = new JSONObject(json);

		} catch (JSONException e) {
			Log.e("JSON Parser", "Error parsing data " + e.toString());
		}
		// return JSON object
		return jObj;
	}
}
