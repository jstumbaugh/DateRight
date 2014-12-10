package com.cse3345.dateright;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

public class UserActions {
	private JSONParser jsonParser;
	private static String loginURL = "http://54.69.57.226/dateright/api/login";
	
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
		List<NameValuePair> params = new ArrayList<NameValuePair>();
		params.add(new BasicNameValuePair("email", email));
		params.add(new BasicNameValuePair("pw", password));
		JSONObject json = jsonParser.getJSONFromUrl(loginURL, params);
		return json;
	}
}
