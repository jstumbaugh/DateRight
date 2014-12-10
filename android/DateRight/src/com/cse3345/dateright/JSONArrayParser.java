package com.cse3345.dateright;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.Map;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

public class JSONArrayParser {
	static InputStream is = null;
	static JSONArray jArr = null;
	static String json = "";

	// constructor
	public JSONArrayParser() {
	}

	public JSONArray getJSONFromUrl(String url, Map<String,String> paramss) {
		// Check to see if were checking to see if the user has a favorite
		try {
			//System.out.println("MAP TESTING");
			//System.out.println("Full: " + paramss.toString());
			//System.out.println("Keys: " + paramss.keySet());
			//for(String s : paramss.keySet()){
				//System.out.println("key: " + s);
			//}

			DefaultHttpClient httpClient = new DefaultHttpClient();
			//HttpPost httpPost = new HttpPost(url);
			HttpResponse httpResponse;
			//parameters means post
			if (paramss != null){
				HttpPost httpPost = new HttpPost(url);

				httpPost.setHeader("Content-type", "application/json");
				httpPost.setHeader("Accept", "application/json");
				
				JSONObject obj = new JSONObject();
				for(String s : paramss.keySet()){
					System.out.println("JSON__key: " + s + " value: " + paramss.get(s).toString());
					obj.put(s, paramss.get(s).toString());
				}
				//obj.put("email",paramss.get("email").toString());
				//obj.put("password",paramss.get("password").toString());
				
				httpPost.setEntity(new StringEntity(obj.toString(), "UTF-8"));
				httpResponse = httpClient.execute(httpPost);
			} else {
				HttpGet httpGet = new HttpGet(url);
				httpResponse = httpClient.execute(httpGet);
			}
			//HttpResponse httpResponse = httpClient.execute(httpPost);
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
				jArr = new JSONArray();
				//jObj.put("emailAlready", true);
			} else
				jArr = new JSONArray(json);

		} catch (JSONException e) {
			Log.e("JSON Parser", "Error parsing data " + e.toString());
		}
		// return JSON object
		return jArr;
	}
}
