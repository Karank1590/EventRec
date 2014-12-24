/**
* Cloud Computing Final Project - Event Recommendation System
* Categorizes facebook interests into predefined categories.
* Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
*/

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;

public class Categorize {

	public static final HashMap<String, String> BOOK_MAP;
	public static final HashMap<String, String> GAME_MAP;
	public static final HashMap<String, String> MOVIE_MAP;
	public static final HashMap<String, String> MUSIC_MAP;
	public static final HashMap<String, String> PAGELIKE_MAP;
	public static final HashMap<String, String> INTEREST_MAP;
	public static final HashMap<String, String> CATEGORY_MAP;
	static {
		BOOK_MAP = new HashMap<String, String>();
		GAME_MAP = new HashMap<String, String>();
		MOVIE_MAP = new HashMap<String, String>();
		MUSIC_MAP = new HashMap<String, String>();
		PAGELIKE_MAP = new HashMap<String, String>();
		INTEREST_MAP = new HashMap<String, String>();
		CATEGORY_MAP = new HashMap<String, String>();

		BOOK_MAP.put("Book", "books");
		BOOK_MAP.put("Book series", "books");
		BOOK_MAP.put("Author", "books");
		BOOK_MAP.put("Public figure", "books");

		GAME_MAP.put("Games/toys", "family_fun_kids");
		GAME_MAP.put("Video game", "family_fun_kids");

		MOVIE_MAP.put("Movie", "movies_film");
		MOVIE_MAP.put("Community", "movies_film");

		MUSIC_MAP.put("Musician/band", "music");
		MUSIC_MAP.put("Artist", "music");
		MUSIC_MAP.put("Community", "music");
		MUSIC_MAP.put("Song", "music");

		PAGELIKE_MAP.put("Games/toys", "family_fun_kids");
		PAGELIKE_MAP.put("Athlete", "sports");
		PAGELIKE_MAP.put("Video game", "family_fun_kids");
		PAGELIKE_MAP.put("Musician/band", "music");
		PAGELIKE_MAP.put("Interest", "None");//
		PAGELIKE_MAP.put("Software", "technology");
		PAGELIKE_MAP.put("Tv show", "movies_film");
		PAGELIKE_MAP.put("Book", "books");
		PAGELIKE_MAP.put("Sport", "sports");
		PAGELIKE_MAP.put("Public figure", "politics_activism");
		PAGELIKE_MAP.put("Movie", "movies_film");
		PAGELIKE_MAP.put("University", "schools_alumni");
		PAGELIKE_MAP.put("Sports team", "sports");
		PAGELIKE_MAP.put("Actor/director", "movies_film");
		PAGELIKE_MAP.put("Media/news/publishing", "other");
		PAGELIKE_MAP.put("School", "schools_alumni");
		PAGELIKE_MAP.put("Journalist", "other");
		PAGELIKE_MAP.put("App page", "other");
		PAGELIKE_MAP.put("Cars", "technology");
		PAGELIKE_MAP.put("Community", "community");
		PAGELIKE_MAP.put("Product/service", "conference");
		PAGELIKE_MAP.put("Food/beverages", "food");
		PAGELIKE_MAP.put("Company", "business");
		PAGELIKE_MAP.put("Education", "learning_education");
		PAGELIKE_MAP.put("Artist", "music");
		PAGELIKE_MAP.put("Local business", "business");
		PAGELIKE_MAP.put("Club", "singles_social");
		PAGELIKE_MAP.put("Arts/entertainment/nightlife", "singles_social");
		PAGELIKE_MAP.put("Non-profit organization", "fundraisers");
		PAGELIKE_MAP.put("Organization", "clubs_associations");
		PAGELIKE_MAP.put("Website", "technology");
		PAGELIKE_MAP.put("Internet/software", "technology");
		PAGELIKE_MAP.put("State/province/region", "community");//
		PAGELIKE_MAP.put("Field of study", "learning_education");
		PAGELIKE_MAP.put("Politician", "politics_activism");
		PAGELIKE_MAP.put("City", "community");//
		PAGELIKE_MAP.put("Baby goods/kids goods", "family_fun_kids");
		PAGELIKE_MAP.put("Author", "books");
		PAGELIKE_MAP.put("Government official", "politics_activism");
		PAGELIKE_MAP.put("Landmark", "attractions");
		PAGELIKE_MAP.put("Government organization", "politics_activism");
		PAGELIKE_MAP.put("Business person", "business");
		PAGELIKE_MAP.put("Political party", "politics_activism");
		PAGELIKE_MAP.put("Aerospace/defense", "technology");
		PAGELIKE_MAP.put("Publisher", "books");
		PAGELIKE_MAP.put("Tv network", "movies_film");
		PAGELIKE_MAP.put("Sports venue", "sports");
		PAGELIKE_MAP.put("Health/medical/pharmaceuticals", "support");
		PAGELIKE_MAP.put("Concert venue", "music");
		PAGELIKE_MAP.put("Country", "community");//
		PAGELIKE_MAP.put("Magazine", "books");
		PAGELIKE_MAP.put("Language", "art");//
		PAGELIKE_MAP.put("Airport", "attractions");
		PAGELIKE_MAP.put("Medical procedure", "support");
		PAGELIKE_MAP.put("Profession", "business");
		PAGELIKE_MAP.put("Tv genre", "movies_film");
		PAGELIKE_MAP.put("Lake", "outdoors_recreation");
		PAGELIKE_MAP.put("Book series", "books");
		PAGELIKE_MAP.put("Entertainment website", "comedy");//
		PAGELIKE_MAP.put("Tours/sightseeing", "attractions");
		PAGELIKE_MAP.put("Computers/internet website", "technology");
		PAGELIKE_MAP.put("Transit stop", "holiday");
		PAGELIKE_MAP.put("Musical instrument", "music");
		PAGELIKE_MAP.put("Religion", "religion_spirituality");
		PAGELIKE_MAP.put("Travel/leisure", "holiday");
		PAGELIKE_MAP.put("Computers/technology", "technology");
		PAGELIKE_MAP.put("Book genre", "books");
		PAGELIKE_MAP.put("Food", "food");
		PAGELIKE_MAP.put("Restaurant/cafe", "food");
		PAGELIKE_MAP.put("Hospital/clinic", "support");
		PAGELIKE_MAP.put("Non-governmental organization (ngo)", "fundraisers");
		PAGELIKE_MAP.put("Education website", "learning_education");
		PAGELIKE_MAP.put("Public places", "outdoors_recreation");
		PAGELIKE_MAP.put("Outdoor gear/sporting goods", "outdoors_recreation");
		PAGELIKE_MAP.put("Small business", "business");
		PAGELIKE_MAP.put("Games/toys", "family_fun_kids");//
		PAGELIKE_MAP.put("Society/culture website", "");
		PAGELIKE_MAP.put("Food/grocery", "food");
		PAGELIKE_MAP.put("Electronics", "technology");
		PAGELIKE_MAP.put("Local/travel website", "holiday");
		PAGELIKE_MAP.put("Sports/recreation/activities", "sports");
		PAGELIKE_MAP.put("Community organization", "community");
		PAGELIKE_MAP.put("Cause", "fundraisers");
		PAGELIKE_MAP.put("Hotel", "holiday");
		PAGELIKE_MAP.put("Retail and consumer merchandise", "sales");
		PAGELIKE_MAP.put("Professional services", "business");
		PAGELIKE_MAP.put("Shopping/retail", "sales");
		PAGELIKE_MAP.put("Bank/financial institution", "business");
		PAGELIKE_MAP.put("Consulting/business services", "business");
		PAGELIKE_MAP.put("Entertainer", "performing_arts");
		PAGELIKE_MAP.put("News/media website", "learning_education");//
		PAGELIKE_MAP.put("Personal blog", "books");//
		PAGELIKE_MAP.put("Board game", "sales");
		PAGELIKE_MAP.put("Song", "music");
		PAGELIKE_MAP.put("Community/government", "community");
		PAGELIKE_MAP.put("Industrials", "conference");
		PAGELIKE_MAP.put("Recreation/sports website", "sports");
		PAGELIKE_MAP.put("Real estate", "community");
		PAGELIKE_MAP.put("Telecommunications", "technology");
		PAGELIKE_MAP.put("Transport/freight", "technology");//
		PAGELIKE_MAP.put("Insurance company", "support");
		PAGELIKE_MAP.put("Library", "books");
		PAGELIKE_MAP.put("Science website", "learning_education");
		PAGELIKE_MAP.put("Teacher", "learning_education");
		PAGELIKE_MAP.put("Teens/kids website", "family_fun_kids");
		PAGELIKE_MAP.put("Household supplies", "sales");
		PAGELIKE_MAP.put("Event planning/event services", "conference");
		PAGELIKE_MAP.put("Clothing", "sales");
		PAGELIKE_MAP.put("Movie teacher", "movies_film");//
		PAGELIKE_MAP.put("Sports event", "sports");
		PAGELIKE_MAP.put("Writer", "books");
		PAGELIKE_MAP.put("Bar", "singles_social");
		PAGELIKE_MAP.put("Health/beauty", "support");
		PAGELIKE_MAP.put("Just for fun", "family_fun_kids");
		PAGELIKE_MAP.put("Tv channel", "movies_film");
		PAGELIKE_MAP.put("School sports team", "sports");
		PAGELIKE_MAP.put("Studio", "movies_film");
		PAGELIKE_MAP.put("Fictional character", "movies_film");//
		PAGELIKE_MAP.put("Health/wellness website", "support");
		PAGELIKE_MAP.put("Tools/equipment", "other");//
		PAGELIKE_MAP.put("Reference website", "learning_education");
		PAGELIKE_MAP.put("Amateur sports team", "sports");
		PAGELIKE_MAP.put("Automobiles and parts", "technology");
		PAGELIKE_MAP.put("Museum/art gallery", "attractions");

		INTEREST_MAP.put("Interest", "None");
		INTEREST_MAP.put("Organization", "fundraisers");
		INTEREST_MAP.put("Software", "technology");
		INTEREST_MAP.put("Landmark", "attractions");
		INTEREST_MAP.put("Field of study", "learning_education");//
		INTEREST_MAP.put("Government organization", "politics_activism");
		INTEREST_MAP.put("Aerospace/defense", "technology");
		INTEREST_MAP.put("City", "community");
		INTEREST_MAP.put("Book", "books");
		INTEREST_MAP.put("University", "schools_alumni");
		INTEREST_MAP.put("Sports team", "sports");
		INTEREST_MAP.put("Lake", "outdoors_recreation");
		INTEREST_MAP.put("Cars", "technology");
		INTEREST_MAP.put("Politician", "politics_activism");
		INTEREST_MAP.put("Tv genre", "movies_film");
		INTEREST_MAP.put("Author", "books");
		INTEREST_MAP.put("Food", "food");

		CATEGORY_MAP.put("attractions", "Museums &amp; Attractions");
		CATEGORY_MAP.put("holiday", "Holiday");
		CATEGORY_MAP.put("family_fun_kids", "Kids &amp; Family");
		CATEGORY_MAP.put("learning_education", "Education");
		CATEGORY_MAP.put("sports", "Sports");
		CATEGORY_MAP.put("performing_arts", "Performing Arts");
		CATEGORY_MAP.put("singles_social", "Nightlife &amp; Singles");
		CATEGORY_MAP.put("business", "Business &amp; Networking");
		CATEGORY_MAP.put("clubs_associations", "Organizations &amp; Meetups");
		CATEGORY_MAP.put("community", "Neighborhood");
		CATEGORY_MAP.put("food", "Food &amp; Wine");
		CATEGORY_MAP.put("music", "Concerts &amp; Tour Dates");
		CATEGORY_MAP.put("art", "Art Galleries &amp; Exhibits");
		CATEGORY_MAP.put("outdoors_recreation", "Outdoors &amp; Recreation");
		CATEGORY_MAP.put("other", "Other &amp; Miscellaneous");
		CATEGORY_MAP.put("support", "Health &amp; Wellness");
		CATEGORY_MAP.put("schools_alumni", "University &amp; Alumni");
		CATEGORY_MAP.put("books", "Literary &amp; Books");
		CATEGORY_MAP.put("conference", "Conferences &amp; Tradeshows");
		CATEGORY_MAP.put("religion_spirituality", "Religion &amp; Spirituality");
		CATEGORY_MAP.put("fundraisers", "Fundraising &amp; Charity");
		CATEGORY_MAP.put("technology", "Technology");
		CATEGORY_MAP.put("sales", "Sales &amp; Retail");
		CATEGORY_MAP.put("politics_activism", "Politics &amp; Activism");
		CATEGORY_MAP.put("None", "None");
		CATEGORY_MAP.put("movies_film", "Film");
		CATEGORY_MAP.put("comedy", "Comedy");
		CATEGORY_MAP.put("science", "Science");

	}

	public static void main(String[] args) {
		try {
			Class.forName("com.mysql.jdbc.Driver");
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
		}

		String jdbcUrl = DATABASE_URL;
		Connection con = null;
		try {
			con = DriverManager.getConnection(jdbcUrl, DATABASE_USERNAME, DATABASE_PASSWORD);

		} catch (SQLException e1) {
			e1.printStackTrace();
		}

		try {
			String query = "SELECT * FROM Books";
			Statement st = con.createStatement();

			ResultSet rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("bid");
				String category = rs.getString("bcategory");
				String name = rs.getString("bname");

				String sql = "insert ignore into books_map (fid, bid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = BOOK_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("Books table completed");

			query = "SELECT * FROM Games";
			st = con.createStatement();
			rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("gid");
				String category = rs.getString("gcategory");
				String name = rs.getString("gname");

				String sql = "insert ignore into games_map (fid, gid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = GAME_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("Games table completed");

			query = "SELECT * FROM Movies";
			st = con.createStatement();

			rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("moid");
				String category = rs.getString("mocategory");
				String name = rs.getString("moname");

				String sql = "insert ignore into movies_map (fid, moid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = MOVIE_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("Movies table completed");

			query = "SELECT * FROM Music";
			st = con.createStatement();
			rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("muid");
				String category = rs.getString("mucategory");
				String name = rs.getString("muname");

				String sql = "insert ignore into music_map (fid, muid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = MUSIC_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("Music table completed");

			query = "SELECT * FROM PageLike";
			st = con.createStatement();
			rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("pid");
				String category = rs.getString("pcategory");
				String name = rs.getString("pname");

				String sql = "insert ignore into pagelike_map (fid, pid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = PAGELIKE_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("PageLike table completed");

			query = "SELECT * FROM Interests";
			st = con.createStatement();
			rs = st.executeQuery(query);

			while (rs.next()) {
				String fid = rs.getString("fid");
				String id = rs.getString("iid");
				String category = rs.getString("icategory");
				String name = rs.getString("iname");

				String sql = "insert ignore into interests_map (fid, iid, cid, cname) values (?, ?, ?, ?)";
				PreparedStatement pst = con.prepareStatement(sql);
				pst.setString(1, fid);
				pst.setString(2, id);
				String cid = INTEREST_MAP.get(category);
				if (cid == null || cid == "") {
					cid = "other";
				}
				pst.setString(3, cid);
				pst.setString(4, CATEGORY_MAP.get(cid));
				pst.executeUpdate();

			}
			st.close();
			System.out.println("Interests table completed");
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
