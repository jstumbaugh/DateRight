package com.cse3345.dateright;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

public class UserActions {
	private JSONParser jsonParser;
	private static String loginURL = "http://54.69.57.226/dateright/api/login";
	private static String logoutURL = "http://54.69.57.226/dateright/api/logout";
	
	public static String KEY_fName = "fname";
	public static String KEY_lName = "lname";
	public static String KEY_email = "email";
	public static String KEY_password = "pw";
	public static String KEY_userID = "userID";
	public static String KEY_username = "username";

	
	public UserActions() {
		jsonParser = new JSONParser();
	}
	
	public JSONObject loginUser(String email, String password) {
		System.out.println("Begin");
		Map<String,String> params = new HashMap();
		params.put("email", email);
		params.put("password", password);
		JSONObject json = jsonParser.getJSONFromUrl(loginURL, params);
		System.out.println("End " + json);
		return json;
	}
		// Sign up user
		public JSONObject signOut() {
			// getting JSON Object
			JSONObject json = jsonParser.getJSONFromUrl(logoutURL, null);
			MainActivity.session.logoutUser();
			// return json
			return json;
		}
}
