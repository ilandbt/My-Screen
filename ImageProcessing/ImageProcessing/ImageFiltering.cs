﻿using System.Threading;
using System.Threading.Tasks;
using System.Diagnostics;
using System.Collections.Specialized;
using System;
using System.Drawing;
using System.Net;
using System.IO;
using System.Text;
using System.Windows.Forms;
using DataHandler;



namespace ImageProcessing
{

    public static class ImageFiltering
    {
        public static String PROGRAM_ID = "1101000100001";
        private static String[] lastURL = new String[10];
        private static int count = 0;
        private static Thread t;

        public static void startFilter()
        {
            t = new Thread(ImageFiltering.ImageFilter);
            t.Start();
        }

        public static void abortThread()
        {
            t.Abort();
        }

        public static void ImageFilter()
        {
            
            String url;
            DataHandler.Messages.init();
            while (true)
            {
                

                url = DataHandler.Messages.read();
               
                if (url != null)
                {
                    //MessageBox.Show("url - " + url);
                   
                    //Console.Write(url + "\n");
                    filter_by_image(url);
                }
            }

        }

        private static void filter_by_image(String url)
        {
            if (url.EndsWith(".jpg"))
            {
                    LocalData.insertUrl(url);
                    filter_by_imageID(url);
                  
            }
        }


        private static void filter_by_imageID(String url)
        {
        

            //if in DB

           // bool decoded = LocalData.getDecodedImageUrl(url);
            bool urls = LocalData.isUrlExist(url);

            //.Show("a = " + a + "\nb = " + b);
            if (!urls)
            {
                LocalData.insertUrl(url);
                Bitmap image = get_image_from_url(url);
                bool result2 = image.Width == 400 && image.Height == 300;

                int program_id = get_program_id(image);
                bool result1 = program_id == Convert.ToInt32(PROGRAM_ID, 2);
                if (result1 && result2)
                {
                    //anlyse image id
                    filter_by_Permissions(image);
                }
            }
        }




        private static void filter_by_Permissions(Bitmap image)
        {

            //check permission from server/Data Base
            int user_id = get_user_id(image);
            uint image_id = get_image_id(image);

            //testing erase next line
            ImageDecoder.decode_image(image, user_id, image_id);


        }

        private static int get_user_id(Bitmap image)
        {
            String str_id = "";
            int pos;
            for (int x = 0; x < 32; x++)
            {
                pos = x + PROGRAM_ID.Length;
                Color temp = image.GetPixel(pos, 0);
                int avg_color = (temp.R + temp.G + temp.B) / 3;
                if (avg_color >= 128)//White
                {
                    str_id += "0";
                }
                else//Black
                {
                    str_id += "1";
                }
            }
            int id = Convert.ToInt32(str_id, 2);
            return id;
        }




        private static uint get_image_id(Bitmap image)
        {
            String str_id = "";
            int pos;
            for (int x = 0; x < 32; x++)
            {
                pos = x + PROGRAM_ID.Length + 32;
                Color temp = image.GetPixel(pos, 0);
                int avg_color = (temp.R + temp.G + temp.B) / 3;
                if (avg_color >= 128)//White
                {
                    str_id += "0";
                }
                else//Black
                {
                    str_id += "1";
                }
            }
            uint id = Convert.ToUInt32(str_id, 2);
            return id;
        }

        private static int get_program_id(Bitmap image)
        {
            String str_id = "";
            int pos;
            for (int x = 0; x < 13; x++)
            {
                pos = x; ;
                Color temp = image.GetPixel(pos, 0);
                int avg_color = (temp.R + temp.G + temp.B) / 3;
                if (avg_color >= 128)//White
                {
                    str_id += "0";
                }
                else//Black
                {
                    str_id += "1";
                }
            }


            int id = Convert.ToInt32(str_id, 2);
            return id;
        }


        private static Bitmap get_image_from_url(String url)
        {

            Bitmap inputBitmap = null;
            using (WebClient wc = new WebClient())
            {
                Stream strm = null;
                try
                {
                    strm = wc.OpenRead(url);

                    Image inputImage = Image.FromStream(strm);
                    inputBitmap = new Bitmap(inputImage);
                }
                finally
                {
                    strm.Close();
                }
            }

            return inputBitmap;
        }

    }
}
