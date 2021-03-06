﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;
using DataHandler;
using System.Threading;

namespace GUI
{
    public static class Program
    {
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        public static void Main()
        {
            DBHandler.initDB();
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new MainForm());
        }
    }
}
