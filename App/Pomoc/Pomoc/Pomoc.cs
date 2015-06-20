using System;
using Geolocator.Plugin;
using Geolocator.Plugin.Abstractions;
using Xamarin.Forms;
using System.Threading.Tasks;
using Toasts.Forms.Plugin.Abstractions;
using System.Timers;

namespace Pomoc
{
	public class App : Application
	{
		Functions Func;

		public App ()
		{
			// The root page of your application
			Func = new Functions();
			MainLayout layout = new MainLayout (Func);
			MainPage = layout;
			Func.setLayout (layout);	
		}

		protected override void OnStart ()
		{
			// Handle when your app starts
			//updateGPS.Start();
			Func.startGPS();
			Console.WriteLine ("GPS started");
		}

		protected override void OnSleep ()
		{
			// Handle when your app sleeps
			//updateGPS.Stop();
			Func.stopGPS();
			Console.WriteLine ("GPS stopped");
		}

		protected override void OnResume ()
		{
			// Handle when your app resumes
			//updateGPS.Start();
			Func.startGPS();
			Console.WriteLine ("GPS started");
		}	
	}

	public class MainLayout: ContentPage
	{
		public int numFire = 150;
		public int numHospital = 155;
		public int numPolice = 158;
		public int numIntegrated = 112;

		public Label acc;
		public Functions Func;

		public MainLayout(Functions funcs)
		{
			Func = funcs;

			Padding = new Thickness(20);

			var police = new Button
			{
				Text = "Policie",
			};
			police.Clicked += (sender, args) => Func.ButtonClick (721438669);

			var fire = new Button
			{
				Text = "Hasiči",
			};
			fire.Clicked += (sender, args) => Func.ButtonClick (721438669);

			var hospital = new Button
			{
				Text = "Záchranka",
			};
			hospital.Clicked += (sender, args) =>Func.ButtonClick (721438669);

			var integral = new Button
			{
				Text = "Integrovaný záchranný systém",
			};
			integral.Clicked += (sender, args) => Func.ButtonClick (721438669);

			var info = new Label {				
				XAlign = TextAlignment.Center,
				FontSize = 25,
				Text = "Aplikace Pomoc Vám může zachránit život!"
			};

			acc = new Label {
				XAlign = TextAlignment.Center,
				FontSize = 15,
				Text = "GPS přesnost"
			};
				

			StackLayout stack = new StackLayout
			{
				Spacing = 20,
				VerticalOptions = LayoutOptions.Center,
				Children = { info, hospital, fire, police, integral, acc}
			};

			Content = new ScrollView {
				Content = stack
			};
		}
	}

	public class Functions
	{
		private MainLayout layout;
		private IGeolocator locator;
		private DateTimeOffset lastTime;

		public void setLayout(MainLayout lay)
		{
			layout = lay;
			locator = CrossGeolocator.Current;
			locator.DesiredAccuracy = 20;
			locator.PositionChanged += updateGPS;
		}

		public void ButtonClick(int number)
		{
			Task<Position> pos = GetLocation ();
			SendLocation ();
			//CallNumber (number);
		}

		public async Task<Position> GetLocation()
		{	
			

			Position position = new Position();

			position = await locator.GetPositionAsync (timeout: 1000);

			Console.WriteLine ("Position Status: {0}", position.Timestamp);
			Console.WriteLine ("Position Latitude: {0}", position.Latitude);
			Console.WriteLine ("Position Longitude: {0}", position.Longitude);
			Console.WriteLine ("Position Accuracy: {0}", position.Accuracy);
			//position.Altitude, position.AltitudeAccuracy, position.Accuracy, position.Heading, position.Speed)



			Device.BeginInvokeOnMainThread(() =>
			{
					layout.acc.Text =  "GPS přesnost: " + position.Accuracy.ToString () + " m (obnoveno po " + ((position.Timestamp - lastTime).Seconds).ToString() + "s)";								
				});

			lastTime = position.Timestamp;

			return position;
		}

		public void startGPS()
		{
			//int minTime, double minDistance, bool includeHeading = false
			locator.StartListening(100,2,false);
		}

		public void stopGPS()
		{
			locator.StopListening ();
		}

		private void updateGPS(object sender, EventArgs e)
		{
			Task<Position> pos = GetLocation ();
		}

		private void SendLocation()
		{


		}

		private void CallNumber(int number)
		{
			Device.OpenUri (new Uri ("tel:+420" + number.ToString()));
		}


		private async void ShowToast(ToastNotificationType type, string sType, string message)
		{
			var notificator = DependencyService.Get<IToastNotificator>();
			bool tapped = await notificator.Notify(type, sType, message, TimeSpan.FromSeconds(2));
		}


	}
}
